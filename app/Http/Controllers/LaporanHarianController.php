<?php

namespace App\Http\Controllers;

use App\Models\LaporanHarian;
use App\Models\Machine;
use App\Models\SparePart;
use App\Models\Line;
use App\Http\Requests\ImportLaporanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class LaporanHarianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Cek permission
        if (!Auth::user()->can('view_own_laporan')) {
            abort(403, 'Unauthorized');
        }

        // Get filter parameters - similar to department head dashboard
        $bulan = $request->input('bulan') ?? now()->month;
        $tahun = $request->input('tahun') ?? now()->year;
        $mesin = $request->input('mesin');
        $line = $request->input('line');
        $showAllTime = $request->input('all_time') == '1';

        // Base query untuk user sendiri (atau semua jika admin)
        $baseQuery = function() use ($tahun, $bulan, $mesin, $line, $showAllTime) {
            $q = LaporanHarian::query();
            
            // Jika bukan admin → hanya lihat laporan sendiri
            if (!Auth::user()->hasRole('admin')) {
                $q->where('user_id', Auth::id());
            }
            
            // Only apply date filters if not showing all time data
            if (!$showAllTime) {
                $q->whereYear('tanggal_laporan', $tahun)
                  ->whereMonth('tanggal_laporan', $bulan);
            }

            if ($mesin) {
                $q->where('mesin_name', $mesin);
            }

            if ($line) {
                $q->where('line', $line);
            }

            return $q;
        };

        // Query untuk metrics
        $query = $baseQuery();
        
        // Total Laporan
        $totalLaporan = $baseQuery()->count();
        
        // Total Downtime (menit) - hanya dari laporan corrective dengan downtime
        $totalDowntimeFailed = $baseQuery()->where('jenis_pekerjaan', 'corrective')->where('downtime_min', '>', 0)->sum('downtime_min') ?? 0;
        $totalDowntime = $totalDowntimeFailed;
        
        // Average MTTR (Mean Time To Repair) - rata-rata dari laporan corrective yang punya downtime
        $avgMTTR = $baseQuery()->where('jenis_pekerjaan', 'corrective')->where('downtime_min', '>', 0)
            ->avg('downtime_min') ?? 0;
        
        // Get Daily Downtime data
        $dailyDowntimes = $baseQuery()
            ->where('downtime_min', '>', 0)
            ->selectRaw('DATE(tanggal_laporan) as date, SUM(downtime_min) as total_downtime')
            ->groupBy(DB::raw('DATE(tanggal_laporan)'))
            ->get();
        
        // Machine Performance Metrics
        $activeMachinesQuery = Machine::where('status', 'active');
        if ($mesin) {
            $activeMachinesQuery->where('name', $mesin);
        }
        $activeMachinesCount = $activeMachinesQuery->count();
        $activeMachinesCount = max(1, $activeMachinesCount);
        
        $totalPlannedTime = 0;
        
        if ($showAllTime) {
            $earliestReport = $baseQuery()->orderBy('tanggal_laporan', 'asc')->first();
            $latestReport = $baseQuery()->orderBy('tanggal_laporan', 'desc')->first();
            
            if ($earliestReport && $latestReport) {
                $startCarbon = \Carbon\Carbon::parse($earliestReport->tanggal_laporan);
                $endCarbon = \Carbon\Carbon::parse($latestReport->tanggal_laporan);
                $totalDays = $endCarbon->diffInDays($startCarbon) + 1;
                $totalPlannedTime = $totalDays * 24 * 60 * $activeMachinesCount;
            }
        } else {
            $daysInMonth = \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;
            $totalPlannedTime = $daysInMonth * 24 * 60 * $activeMachinesCount;
        }
        
        // Total Breakdown
        $totalBreakdown = $baseQuery()->where('jenis_pekerjaan', 'corrective')->where('downtime_min', '>', 0)->count();
        
        $totalDowntimeMinutes = $totalDowntimeFailed;
        
        // Ensure values are positive and valid
        $totalPlannedTime = max(0, $totalPlannedTime);
        $totalDowntimeMinutes = max(0, $totalDowntimeMinutes);
        
        // Hitung Availability dan Downtime Percentage
        $downtimePercent = $totalPlannedTime > 0 ? ($totalDowntimeMinutes / $totalPlannedTime) * 100 : 0;
        $downtimePercent = min(100, $downtimePercent);
        $availability = 100 - $downtimePercent;
        
        // Maintenance Types (Convert menit to jam)
        $totalCorrectiveMaint = ($baseQuery()->where('jenis_pekerjaan', 'corrective')->sum('downtime_min') ?? 0) / 60;
        $totalPreventiveMaint = ($baseQuery()->where('jenis_pekerjaan', 'preventive')->sum('downtime_min') ?? 0) / 60;
        $totalChangeOver = ($baseQuery()->where('jenis_pekerjaan', 'change over product')->sum('downtime_min') ?? 0) / 60;
        
        // Top 10 Mesin dengan downtime terbanyak
        $topDowntimeMesin = $baseQuery()->select('mesin_name', DB::raw('SUM(downtime_min) as total_downtime'))
            ->groupBy('mesin_name')
            ->orderByDesc('total_downtime')
            ->limit(10)
            ->get();
        
        // All Breakdown by Line (showing all lines including those with 0 breakdown count)
        $allLines = Line::get();
        $breakdownByLine = $baseQuery()
            ->select('line', DB::raw('COUNT(*) as breakdown_count'))
            ->groupBy('line')
            ->get()
            ->keyBy('line');
        
        // Combine all lines with breakdown data (including 0 counts)
        $topBreakdownLine = $allLines->map(function($line) use ($breakdownByLine) {
            $breakdown = $breakdownByLine->get($line->name);
            return (object)[
                'line' => $line->name,
                'breakdown_count' => $breakdown ? $breakdown->breakdown_count : 0
            ];
        })->sortByDesc('breakdown_count');
        
        // Top 7 Breakdown by Catatan
        $topBreakdownCatatan = $baseQuery()->select('catatan', DB::raw('COUNT(*) as breakdown_count'))
            ->whereNotNull('catatan')
            ->where('catatan', '<>', '')
            ->groupBy('catatan')
            ->orderByDesc('breakdown_count')
            ->limit(7)
            ->get();
        
        // Monitoring Spare Part
        $spareParts = $baseQuery()->select('sparepart', DB::raw('SUM(qty_sparepart) as total_qty'))
            ->whereNotNull('sparepart')
            ->where('sparepart', '<>', '')
            ->groupBy('sparepart')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();
        
        // Machine Performance by Type
        $machinePerformance = $baseQuery()->select('mesin_name', DB::raw('COUNT(*) as count'))
            ->groupBy('mesin_name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Get unique values untuk filter
        $allMesins = LaporanHarian::distinct()->pluck('mesin_name')->sort();
        $allLines = LaporanHarian::distinct()->pluck('line')->sort();

        // MTBF Metrics dari Machine Model
        $machines = Machine::where('status', 'active')->with('line')->get();
        $mtbfData = [];
        $totalMTBFHours = 0;
        $mtbfMachineCount = 0;

        foreach ($machines as $machine) {
            if ($showAllTime) {
                $mtbf = $machine->calculateMTBFAllTime();
            } else {
                $mtbf = $machine->calculateMTBF($tahun, $bulan);
            }
            if ($mtbf['failure_count'] > 0) {
                $mtbfData[] = $mtbf;
                $totalMTBFHours += $mtbf['mtbf_hours'];
                $mtbfMachineCount++;
            }
        }

        // Sort by MTBF descending
        usort($mtbfData, function ($a, $b) {
            return $b['mtbf_hours'] <=> $a['mtbf_hours'];
        });

        // Average MTBF
        $avgMTBFHours = $mtbfMachineCount > 0 ? $totalMTBFHours / $mtbfMachineCount : 0;

        // Get top machines by reliability
        $topReliableMachines = array_slice($mtbfData, 0, 5);
        $worstMachines = array_slice(array_reverse($mtbfData), 0, 5);

        // Get downtime by scope (Electrical, Mechanical, Utility, Building)
        $downtimeByScope = $this->getDowntimeByScope($tahun, $bulan, function() use ($baseQuery) {
            return $baseQuery();
        });

        return view('laporan.index', compact(
            'totalLaporan',
            'totalDowntime',
            'totalDowntimeMinutes',
            'avgMTTR',
            'avgMTBFHours',
            'availability',
            'downtimePercent',
            'topDowntimeMesin',
            'topBreakdownLine',
            'topBreakdownCatatan',
            'spareParts',
            'machinePerformance',
            'totalPlannedTime',
            'totalBreakdown',
            'totalCorrectiveMaint',
            'totalPreventiveMaint',
            'totalChangeOver',
            'bulan',
            'tahun',
            'mesin',
            'line',
            'allMesins',
            'allLines',
            'mtbfData',
            'topReliableMachines',
            'worstMachines',
            'downtimeByScope'
        ));
    }

    /**
     * Display a simple list of laporan for the current user
     */
    public function list(Request $request)
    {
        // Cek permission
        if (!Auth::user()->can('view_own_laporan')) {
            abort(403, 'Unauthorized');
        }

        // Get filter parameters
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun') ?? now()->year;
        $mesin = $request->input('mesin');
        $line = $request->input('line');
        $search = $request->input('search');

        // Build query
        $query = LaporanHarian::query();

        // Filter by user (non-admin users only see their own)
        if (!Auth::user()->hasRole('admin')) {
            $query->where('user_id', Auth::id());
        }

        // Apply filters
        if ($bulan) {
            $query->whereMonth('tanggal_laporan', $bulan);
        }
        if ($tahun) {
            $query->whereYear('tanggal_laporan', $tahun);
        }
        if ($mesin) {
            $query->where('mesin_name', $mesin);
        }
        if ($line) {
            $query->where('line', $line);
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('mesin_name', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhere('line', 'like', "%{$search}%");
            });
        }

        // Get unique values for filters
        $allMesins = LaporanHarian::distinct()->pluck('mesin_name')->sort();
        $allLines = LaporanHarian::distinct()->pluck('line')->sort();
        
        // Get months list
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Paginate results
        $laporan = $query->orderBy('tanggal_laporan', 'desc')
                         ->paginate(20)
                         ->appends($request->query());

        return view('laporan.list', compact(
            'laporan',
            'allMesins',
            'allLines',
            'bulanList',
            'bulan',
            'tahun',
            'mesin',
            'line',
            'search'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check permission
        if (!Auth::user()->can('create_laporan')) {
            abort(403, 'Unauthorized');
        }

        // Get list of lines
        $lines = Line::where('status', 'active')->get();

        // Get list of machines
        $machines = Machine::where('status', 'active')->get();
        
        // Get list of spare parts
        $spareParts = SparePart::where('status', 'active')->get();

        return view('laporan.create', compact('lines', 'machines', 'spareParts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check permission
        if (!Auth::user()->can('create_laporan')) {
            abort(403, 'Unauthorized');
        }

        // First validate jenis_pekerjaan first
        $jenisPekerjaan = $request->input('jenis_pekerjaan');
        $rules = [
            'machine_id' => 'integer|exists:machines,id',
            'line_id' => 'required|integer|exists:lines,id',
            'catatan' => 'nullable|string',
            'spare_part_id' => 'nullable|integer|exists:spare_parts,id',
            'qty_sparepart' => 'numeric|min:0',
            'komentar_sparepart' => 'nullable|string',
            'spare_parts_used' => 'nullable|string',
            'jenis_pekerjaan' => 'required|in:corrective,preventive,change over product,modifikasi,utility',
            'scope' => 'required|in:Electrik,Mekanik,Utility,Building',
            'downtime_min' => 'integer|min:0',
            'tipe_laporan' => 'in:harian,mingguan,bulanan',
            'tanggal_laporan' => 'required|date',
        ];

        // Add required validation for start_time and end_time untuk corrective, preventive, dan change over product
        if (in_array($jenisPekerjaan, ['corrective', 'preventive', 'change over product'])) {
            $rules['start_time'] = 'required|date_format:Y-m-d\TH:i';
            $rules['end_time'] = 'required|date_format:Y-m-d\TH:i|after:start_time';
        } else {
            $rules['start_time'] = 'nullable|date_format:Y-m-d\TH:i';
            $rules['end_time'] = 'nullable|date_format:Y-m-d\TH:i|after:start_time';
        }

        $validated = $request->validate($rules);

        $validated['user_id'] = Auth::id();
        
        // Get machine name from selected machine and line name from line
        if ($validated['machine_id']) {
            $machine = Machine::find($validated['machine_id']);
            $validated['mesin_name'] = $machine->name;
        }
        
        // Get line name from selected line
        if ($validated['line_id']) {
            $line = Line::find($validated['line_id']);
            $validated['line'] = $line->name;
        }

        // Calculate downtime for corrective, preventive and change over product types
        if (($validated['jenis_pekerjaan'] === 'corrective' || $validated['jenis_pekerjaan'] === 'preventive' || $validated['jenis_pekerjaan'] === 'change over product') && isset($validated['start_time']) && isset($validated['end_time'])) {
            $start = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['start_time']);
            $end = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['end_time']);
            $validated['downtime_min'] = (int) $start->diffInMinutes($end);
        } else {
            $validated['downtime_min'] = 0;
        }
        
        // Handle multiple spare parts (new feature)
        if (!empty($validated['spare_parts_used'])) {
            \Log::info('==== SPARE PARTS PROCESSING START ====');
            \Log::info('Received spare_parts_used value:', ['value' => $validated['spare_parts_used']]);
            
            try {
                $sparePartsList = json_decode($validated['spare_parts_used'], true);
                \Log::info('Decoded spare parts list:', ['decoded' => $sparePartsList]);
                
                if (is_array($sparePartsList) && !empty($sparePartsList)) {
                    // Update stock for each spare part
                    foreach ($sparePartsList as $index => $sparePartData) {
                        \Log::info("Processing spare part #{$index}:", [
                            'id' => $sparePartData['id'],
                            'name' => $sparePartData['name'] ?? 'N/A',
                            'qty' => $sparePartData['qty']
                        ]);
                        
                        $sparePart = SparePart::find($sparePartData['id']);
                        \Log::info("SparePart found:", [
                            'found' => $sparePart ? 'YES' : 'NO',
                            'current_stock' => $sparePart ? $sparePart->stock : null,
                            'name' => $sparePart ? $sparePart->name : null
                        ]);
                        
                        if ($sparePart) {
                            $oldStock = $sparePart->stock;
                            $newStock = $oldStock - $sparePartData['qty'];
                            
                            \Log::info("Stock calculation:", [
                                'old_stock' => $oldStock,
                                'qty_to_reduce' => $sparePartData['qty'],
                                'new_stock' => $newStock
                            ]);
                            
                            if ($newStock < 0) {
                                \Log::warning("Stock insufficient for {$sparePart->name}");
                                return redirect()->back()
                                    ->withErrors(['spare_parts_used' => "Stok {$sparePart->name} tidak cukup! Stok tersedia: {$oldStock}"])
                                    ->withInput();
                            }
                            
                            // Direct update using raw query for debugging
                            $updated = $sparePart->update(['stock' => $newStock]);
                            
                            \Log::info("Update result:", [
                                'updated' => $updated ? 'YES' : 'NO',
                                'spare_part_id' => $sparePart->id,
                                'new_stock' => $newStock
                            ]);
                            
                            // Verify the update
                            $sparePart->refresh();
                            \Log::info("After refresh:", [
                                'stock' => $sparePart->stock
                            ]);
                        }
                    }
                    
                    // Set first spare part for backward compatibility
                    if (isset($sparePartsList[0])) {
                        $validated['spare_part_id'] = $sparePartsList[0]['id'];
                        $validated['qty_sparepart'] = $sparePartsList[0]['qty'];
                        $validated['komentar_sparepart'] = $sparePartsList[0]['komentar'] ?? '';
                    }
                } else {
                    \Log::warning('Decoded data is not array or is empty');
                }
            } catch (\Exception $e) {
                \Log::error('Error processing spare parts', [
                    'exception' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()
                    ->withErrors(['spare_parts_used' => 'Error processing spare parts: ' . $e->getMessage()])
                    ->withInput();
            }
            \Log::info('==== SPARE PARTS PROCESSING END ====');
        }
        // Handle single spare part (backward compatibility)
        else if ((isset($validated['spare_part_id']) ? $validated['spare_part_id'] : null) && isset($validated['qty_sparepart']) && $validated['qty_sparepart'] > 0) {
            \Log::info('Processing single spare part (backward compatibility)');
            $sparePart = SparePart::find($validated['spare_part_id']);
            
            if ($sparePart) {
                $oldStock = $sparePart->stock;
                $newStock = $oldStock - $validated['qty_sparepart'];
                
                if ($newStock < 0) {
                    return redirect()->back()
                        ->withErrors(['qty_sparepart' => "Stok spare part tidak cukup! Stok tersedia: {$oldStock}"])
                        ->withInput();
                }
                
                $sparePart->update(['stock' => $newStock]);
                \Log::info('Single spare part stock updated', [
                    'spare_part' => $sparePart->name,
                    'old_stock' => $oldStock,
                    'new_stock' => $newStock
                ]);
            }
        } else {
            \Log::info('No spare parts to process - both multiple and single are empty');
        }
        
        LaporanHarian::create($validated);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $laporan = LaporanHarian::with(['machine', 'line', 'sparePart', 'user'])->findOrFail($id);

        // Check permission
        if ($laporan->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        return view('laporan.show', compact('laporan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $laporan = LaporanHarian::findOrFail($id);
        
        // Check permission
        if ($laporan->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        if (!Auth::user()->can('edit_laporan')) {
            abort(403, 'Unauthorized');
        }

        // Get list of lines
        $lines = Line::where('status', 'active')->get();

        // Get list of machines
        $machines = Machine::where('status', 'active')->get();

        // Get list of spare parts
        $spareParts = SparePart::where('status', 'active')->get();

        return view('laporan.edit', compact('laporan', 'lines', 'machines', 'spareParts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $laporan = LaporanHarian::findOrFail($id);

        // Check permission
        if ($laporan->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        if (!Auth::user()->can('edit_laporan')) {
            abort(403, 'Unauthorized');
        }

        // First validate jenis_pekerjaan first
        $jenisPekerjaan = $request->input('jenis_pekerjaan');
        $rules = [
            'machine_id' => 'integer|exists:machines,id',
            'line_id' => 'required|integer|exists:lines,id',
            'catatan' => 'nullable|string',
            'spare_part_id' => 'nullable|integer|exists:spare_parts,id',
            'qty_sparepart' => 'integer|min:0',
            'komentar_sparepart' => 'nullable|string',
            'jenis_pekerjaan' => 'required|in:corrective,preventive,change over product,modifikasi,utility',
            'scope' => 'required|in:Electrik,Mekanik,Utility,Building',
            'downtime_min' => 'integer|min:0',
            'tipe_laporan' => 'in:harian,mingguan,bulanan',
            'tanggal_laporan' => 'required|date',
        ];

        // Add required validation for start_time and end_time untuk corrective, preventive, dan change over product
        if (in_array($jenisPekerjaan, ['corrective', 'preventive', 'change over product'])) {
            $rules['start_time'] = 'required|date_format:Y-m-d\TH:i';
            $rules['end_time'] = 'required|date_format:Y-m-d\TH:i|after:start_time';
        } else {
            $rules['start_time'] = 'nullable|date_format:Y-m-d\TH:i';
            $rules['end_time'] = 'nullable|date_format:Y-m-d\TH:i|after:start_time';
        }

        $validated = $request->validate($rules);

        // Get machine name from selected machine
        if ($validated['machine_id']) {
            $machine = Machine::find($validated['machine_id']);
            $validated['mesin_name'] = $machine->name;
        }

        // Get line name from selected line
        if ($validated['line_id']) {
            $line = Line::find($validated['line_id']);
            $validated['line'] = $line->name;
        }

        // Calculate downtime for corrective, preventive and change over product types
        if (($validated['jenis_pekerjaan'] === 'corrective' || $validated['jenis_pekerjaan'] === 'preventive' || $validated['jenis_pekerjaan'] === 'change over product') && isset($validated['start_time']) && isset($validated['end_time'])) {
            $start = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['start_time']);
            $end = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['end_time']);
            $validated['downtime_min'] = (int) $start->diffInMinutes($end);
        } else {
            $validated['downtime_min'] = 0;
        }

        $laporan->update($validated);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $laporan = LaporanHarian::findOrFail($id);

        // Check permission
        if ($laporan->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        if (!Auth::user()->can('delete_laporan')) {
            abort(403, 'Unauthorized');
        }

        // Restore stok spare part jika ada
        if ($laporan->spare_part_id && $laporan->qty_sparepart > 0) {
            $sparePart = SparePart::find($laporan->spare_part_id);
            if ($sparePart) {
                $sparePart->update(['stock' => $sparePart->stock + $laporan->qty_sparepart]);
            }
        }

        $laporan->delete();

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dihapus!');
    }

    /**
     * Clear all laporan records
     */
    public function clearAll()
    {
        // Check permission
        if (!Auth::user()->can('delete_laporan')) {
            abort(403, 'Unauthorized');
        }

        // Only allow admins to clear all, others can only clear their own
        if (Auth::user()->hasRole('admin')) {
            // Admin: clear all laporan
            $deletedCount = LaporanHarian::count();
            LaporanHarian::truncate();
        } else {
            // Non-admin: clear only their laporan
            $deletedCount = LaporanHarian::where('user_id', Auth::id())->count();
            LaporanHarian::where('user_id', Auth::id())->delete();
        }

        return redirect()->route('laporan.index')->with('success', "Berhasil menghapus {$deletedCount} laporan!");
    }

    public function importForm()
    {
        return view('laporan.import');
    }

    public function import(ImportLaporanRequest $request)
    {
        try {
            $file = $request->file('file');
            $successCount = 0;
            $skipCount = 0;
            $errorMessages = [];
            $infoMessages = [];

            // Load spreadsheet using PhpOffice
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = [];
            $headerRow = null;
            
            // Extract data from spreadsheet
            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }
                
                if ($rowIndex === 1) {
                    // First row is header - process and normalize
                    $headerRow = [];
                    foreach ($rowData as $header) {
                        // Normalize: lowercase, trim, remove descriptions in parentheses
                        $cleanHeader = strtolower(trim($header));
                        // Remove text in parentheses
                        $cleanHeader = preg_replace('/\s*\([^)]*\)/', '', $cleanHeader);
                        $cleanHeader = trim($cleanHeader);
                        
                        // Map alternative header names to standard ones
                        $headerMap = [
                            'tanggal' => 'tanggal_laporan',
                            'mesin' => 'machine_name',
                            'machine' => 'machine_name',
                            'nama mesin' => 'machine_name',
                            'line' => 'line_name',
                            'nama line' => 'line_name',
                            'jenis' => 'jenis_pekerjaan',
                            'type pekerjaan' => 'jenis_pekerjaan',
                            'scope pekerjaan' => 'scope',
                            'catatan' => 'notes',
                            'note' => 'notes',
                            'sparepart' => 'spare_part_name',
                            'spare part' => 'spare_part_name',
                            'qty' => 'qty_spare_part',
                            'quantity' => 'qty_spare_part',
                            'komentar' => 'spare_part_notes',
                            'start' => 'start_time',
                            'waktu mulai' => 'start_time',
                            'end' => 'end_time',
                            'waktu selesai' => 'end_time',
                            'downtime' => 'downtime_min',
                            'status laporan' => 'status',
                            'tipe' => 'report_type',
                            'type' => 'report_type',
                        ];
                        
                        // Check if header needs mapping
                        if (isset($headerMap[$cleanHeader])) {
                            $cleanHeader = $headerMap[$cleanHeader];
                        }
                        
                        $headerRow[] = $cleanHeader;
                    }
                } else {
                    // Subsequent rows are data
                    if (!empty(array_filter($rowData))) { // Skip empty rows
                        $rows[] = array_combine($headerRow, $rowData);
                    }
                }
            }
            
            if (empty($rows)) {
                return redirect()->route('laporan.index')->with('error', 'File Excel kosong atau format tidak sesuai');
            }

            foreach ($rows as $index => $row) {
                try {
                    if (empty($row['tanggal_laporan']) && empty($row['machine_name'])) {
                        $skipCount++;
                        continue;
                    }

                    // Get machine by name
                    $machine = null;
                    $line = null;
                    $sparePart = null;

                    // Process line first (because machine needs line_id)
                    if (!empty($row['line_name'])) {
                        $line = Line::where('name', trim($row['line_name']))->first();
                        if (!$line) {
                            // Auto-create line if doesn't exist
                            $line = Line::create([
                                'name' => trim($row['line_name']),
                                'code' => strtoupper(str_replace(' ', '_', trim($row['line_name']))),
                                'status' => 'active',
                            ]);
                            $infoMessages[] = "Baris " . ($index + 2) . ": Line '{$row['line_name']}' baru dibuat otomatis";
                        }
                    }

                    if (!empty($row['machine_name'])) {
                        $machine = Machine::where('name', trim($row['machine_name']))->first();
                        if (!$machine) {
                            // Auto-create machine if doesn't exist
                            $machine = Machine::create([
                                'name' => trim($row['machine_name']),
                                'code' => strtoupper(str_replace(' ', '_', trim($row['machine_name']))),
                                'line_id' => $line ? $line->id : null,
                                'status' => 'active',
                            ]);
                            $infoMessages[] = "Baris " . ($index + 2) . ": Mesin '{$row['machine_name']}' baru dibuat otomatis";
                        }
                        
                        // Update line from machine if not already set
                        if (!$line && $machine->line) {
                            $line = $machine->line;
                        }
                    }

                    if (!empty($row['spare_part_name'])) {
                        $sparePart = SparePart::where('name', trim($row['spare_part_name']))->first();
                        if (!$sparePart) {
                            // Auto-create spare part if doesn't exist
                            $sparePart = SparePart::create([
                                'name' => trim($row['spare_part_name']),
                                'code' => strtoupper(str_replace(' ', '_', trim($row['spare_part_name']))),
                                'status' => 'active',
                            ]);
                            $infoMessages[] = "Baris " . ($index + 2) . ": Spare Part '{$row['spare_part_name']}' baru dibuat otomatis";
                        }
                    }

                    // Parse tanggal - support multiple formats including Excel serial dates
                    $tanggalLaporan = null;
                    if (!empty($row['tanggal_laporan'])) {
                        try {
                            $dateValue = trim($row['tanggal_laporan']);
                            
                            // Check if it's a numeric value (Excel serial date)
                            if (is_numeric($dateValue) && (int)$dateValue > 0) {
                                try {
                                    // Convert Excel serial date to DateTime
                                    $dateObj = Date::excelToDateTimeObject($dateValue);
                                    $tanggalLaporan = $dateObj->format('Y-m-d');
                                } catch (\Exception $ee) {
                                    throw new \Exception("Tidak bisa mengkonversi Excel date: {$dateValue}");
                                }
                            } else {
                                // Try multiple text date formats
                                $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d', 'd.m.Y', 'j/n/Y', 'j-n-Y'];
                                $tanggalLaporan = null;
                                
                                foreach ($formats as $format) {
                                    try {
                                        $tanggalLaporan = Carbon::createFromFormat($format, $dateValue)->toDateString();
                                        break;
                                    } catch (\Exception $fe) {
                                        // Try next format
                                    }
                                }
                                
                                if (!$tanggalLaporan) {
                                    throw new \Exception("Format tanggal tidak valid: {$dateValue}");
                                }
                            }
                        } catch (\Exception $e) {
                            $skipCount++;
                            $errorMessages[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                            continue;
                        }
                    } else {
                        // If no date provided, use today's date
                        $tanggalLaporan = Carbon::now()->toDateString();
                    }

                    // Parse start time dan end time
                    $startTime = null;
                    $endTime = null;
                    
                    // Parse and validate jenis_pekerjaan
                    $jenisPekerjaanRaw = trim($row['jenis_pekerjaan'] ?? 'preventive');
                    // Remove extra whitespace and convert to lowercase
                    $jenisPekerjaan = strtolower(preg_replace('/\s+/', '', $jenisPekerjaanRaw));
                    
                    // Normalize jenis_pekerjaan - allow variations
                    $jenisPekerjaanMap = [
                        'corrective' => 'corrective',
                        'corr' => 'corrective',
                        'perbaikan' => 'corrective',
                        'preventive' => 'preventive',
                        'prev' => 'preventive',
                        'pencegahan' => 'preventive',
                        'pemeliharaan' => 'preventive',
                        'modifikasi' => 'modifikasi',
                        'mod' => 'modifikasi',
                        'utility' => 'utility',
                        'util' => 'utility',
                    ];
                    
                    if (isset($jenisPekerjaanMap[$jenisPekerjaan])) {
                        $jenisPekerjaan = $jenisPekerjaanMap[$jenisPekerjaan];
                    } else {
                        // Default to preventive if unknown
                        $infoMessages[] = "Baris " . ($index + 2) . ": Jenis pekerjaan '{$jenisPekerjaanRaw}' tidak dikenal, menggunakan 'preventive'";
                        $jenisPekerjaan = 'preventive';
                    }

                    // Parse start time dengan multiple format support
                    if (!empty($row['start_time'])) {
                        try {
                            $timeValue = trim($row['start_time']);
                            
                            // Try to parse as Excel decimal time or numeric value first
                            if (is_numeric($timeValue)) {
                                $numValue = (float)$timeValue;
                                // If it's a decimal between 0 and 1, it's Excel time format
                                if ($numValue > 0 && $numValue < 1) {
                                    $totalSeconds = $numValue * 24 * 60 * 60;
                                    $hours = floor($totalSeconds / 3600);
                                    $minutes = floor(($totalSeconds % 3600) / 60);
                                    $seconds = floor($totalSeconds % 60);
                                    $startTime = Carbon::createFromTime($hours, $minutes, $seconds);
                                } else {
                                    $startTime = null;
                                }
                            } else {
                                // Try multiple text time formats
                                $timeFormats = ['H:i', 'H:i:s', 'HH:mm', 'HH:mm:ss', 'h:i A', 'h:i:s A', 'H.i', 'H.i.s'];
                                $startTime = null;
                                
                                foreach ($timeFormats as $format) {
                                    try {
                                        $startTime = Carbon::createFromFormat($format, $timeValue);
                                        break;
                                    } catch (\Exception $fe) {
                                        // Try next format
                                    }
                                }
                            }
                            
                            if (!$startTime) {
                                $infoMessages[] = "Baris " . ($index + 2) . ": Format start_time tidak valid: {$timeValue}";
                            }
                        } catch (\Exception $e) {
                            $infoMessages[] = "Baris " . ($index + 2) . ": Error parsing start_time";
                        }
                    }

                    // Parse end time dengan multiple format support
                    if (!empty($row['end_time'])) {
                        try {
                            $timeValue = trim($row['end_time']);
                            
                            // Try to parse as Excel decimal time or numeric value first
                            if (is_numeric($timeValue)) {
                                $numValue = (float)$timeValue;
                                // If it's a decimal between 0 and 1, it's Excel time format
                                if ($numValue > 0 && $numValue < 1) {
                                    $totalSeconds = $numValue * 24 * 60 * 60;
                                    $hours = floor($totalSeconds / 3600);
                                    $minutes = floor(($totalSeconds % 3600) / 60);
                                    $seconds = floor($totalSeconds % 60);
                                    $endTime = Carbon::createFromTime($hours, $minutes, $seconds);
                                } else {
                                    $endTime = null;
                                }
                            } else {
                                // Try multiple text time formats
                                $timeFormats = ['H:i', 'H:i:s', 'HH:mm', 'HH:mm:ss', 'h:i A', 'h:i:s A', 'H.i', 'H.i.s'];
                                $endTime = null;
                                
                                foreach ($timeFormats as $format) {
                                    try {
                                        $endTime = Carbon::createFromFormat($format, $timeValue);
                                        break;
                                    } catch (\Exception $fe) {
                                        // Try next format
                                    }
                                }
                            }
                            
                            if (!$endTime) {
                                $infoMessages[] = "Baris " . ($index + 2) . ": Format end_time tidak valid: {$timeValue}";
                            }
                        } catch (\Exception $e) {
                            $infoMessages[] = "Baris " . ($index + 2) . ": Error parsing end_time";
                        }
                    }

                    // Calculate downtime from start_time and end_time if both available
                    $downtimeMin = !empty($row['downtime_min']) ? (int)$row['downtime_min'] : 0;
                    if ($startTime && $endTime) {
                        $downtimeMin = $startTime->diffInMinutes($endTime);
                    }

                    // Parse and normalize tipe_laporan
                    $tipeLaporanRaw = trim($row['report_type'] ?? 'harian');
                    $tipeLaporanNormalized = strtolower(preg_replace('/\s+/', '', $tipeLaporanRaw));
                    $tipeLaporanMap = [
                        'harian' => 'harian',
                        'daily' => 'harian',
                        'mingguan' => 'mingguan',
                        'weekly' => 'mingguan',
                        'bulanan' => 'bulanan',
                        'monthly' => 'bulanan',
                    ];
                    $tipeLaporan = $tipeLaporanMap[$tipeLaporanNormalized] ?? 'harian';

                    // Parse and normalize status
                    $statusRaw = strtolower(preg_replace('/\s+/', '', trim($row['status'] ?? 'completed')));
                    $status = ($statusRaw === 'pending') ? 'pending' : 'completed';

                    // Parse and normalize scope 
                    $scopeRaw = trim($row['scope'] ?? '');
                    $scopeNormalized = strtolower(preg_replace('/\s+/', '', $scopeRaw));
                    $scopeMap = [
                        'electrik' => 'Electrik',
                        'mekanik' => 'Mekanik',
                        'mechanical' => 'Mekanik',
                        'utility' => 'Utility',
                        'building' => 'Building',
                        'bangunan' => 'Building',
                    ];
                    $scope = $scopeMap[$scopeNormalized] ?? ($scopeRaw ? ucfirst($scopeRaw) : '');

                    LaporanHarian::create([
                        'user_id' => Auth::id(),
                        'machine_id' => $machine ? $machine->id : null,
                        'line_id' => $line ? $line->id : null,
                        'spare_part_id' => $sparePart ? $sparePart->id : null,
                        'mesin_name' => trim($row['machine_name'] ?? ''),
                        'line' => trim($row['line_name'] ?? ''),
                        'catatan' => trim($row['notes'] ?? ''),
                        'sparepart' => trim($row['spare_part_name'] ?? ''),
                        'qty_sparepart' => !empty($row['qty_spare_part']) ? (int)$row['qty_spare_part'] : 0,
                        'komentar_sparepart' => trim($row['spare_part_notes'] ?? ''),
                        'status' => $status,
                        'jenis_pekerjaan' => $jenisPekerjaan,
                        'scope' => $scope,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'downtime_min' => $downtimeMin,
                        'tipe_laporan' => $tipeLaporan,
                        'tanggal_laporan' => $tanggalLaporan,
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $skipCount++;
                    $errorMessages[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Import selesai! {$successCount} laporan berhasil ditambahkan.";
            if ($skipCount > 0) {
                $message .= " {$skipCount} baris dilewati.";
            }

            // Show info messages
            if (!empty($infoMessages)) {
                $message .= "\n\nData baru dibuat otomatis (" . count($infoMessages) . "):";
                foreach (array_slice($infoMessages, 0, 10) as $info) {
                    $message .= "\n• " . $info;
                }
                if (count($infoMessages) > 10) {
                    $message .= "\n... dan " . (count($infoMessages) - 10) . " data lainnya";
                }
            }

            // Show error messages
            if (!empty($errorMessages)) {
                $message .= "\n\nError (" . count($errorMessages) . "):";
                foreach (array_slice($errorMessages, 0, 5) as $err) {
                    $message .= "\n• " . $err;
                }
                if (count($errorMessages) > 5) {
                    $message .= "\n... dan " . (count($errorMessages) - 5) . " error lainnya";
                }
            }

            return redirect()->route('laporan.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('laporan.index')->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }

    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'tanggal_laporan',
            'machine_name',
            'line_name',
            'line_status',
            'jenis_pekerjaan',
            'scope',
            'notes',
            'spare_part_name',
            'qty_spare_part',
            'spare_part_notes',
            'start_time',
            'end_time',
            'downtime_min',
            'status',
            'report_type',
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1) . '1', $header);
        }

        // Format header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
        ];
        $sheet->getStyle('A1:O1')->applyFromArray($headerStyle);

        // Set column width
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(12);  // line_status
        $sheet->getColumnDimension('E')->setWidth(18);  // jenis_pekerjaan
        $sheet->getColumnDimension('F')->setWidth(20);  // scope
        $sheet->getColumnDimension('G')->setWidth(20);  // notes
        $sheet->getColumnDimension('H')->setWidth(20);  // spare_part_name
        $sheet->getColumnDimension('I')->setWidth(15);  // qty_spare_part
        $sheet->getColumnDimension('J')->setWidth(20);  // spare_part_notes
        $sheet->getColumnDimension('K')->setWidth(12);  // start_time
        $sheet->getColumnDimension('L')->setWidth(12);  // end_time
        $sheet->getColumnDimension('M')->setWidth(12);  // downtime_min
        $sheet->getColumnDimension('N')->setWidth(12);  // status
        $sheet->getColumnDimension('O')->setWidth(12);  // report_type

        // Add sample data - Row 2: Preventive maintenance
        $sheet->setCellValue('A2', '15/06/2026');
        $sheet->setCellValue('B2', 'Mesin Produksi A1');
        $sheet->setCellValue('C2', 'Line A');
        $sheet->setCellValue('D2', 'on');  // line_status - line ini dihitung downtime-nya
        $sheet->setCellValue('E2', 'preventive');
        $sheet->setCellValue('F2', 'Mechanical');
        $sheet->setCellValue('G2', 'Rutin harian - Pembersihan dan pelumasan');
        $sheet->setCellValue('H2', '');
        $sheet->setCellValue('I2', '');
        $sheet->setCellValue('J2', '');
        $sheet->setCellValue('K2', '');
        $sheet->setCellValue('L2', '');
        $sheet->setCellValue('M2', '');
        $sheet->setCellValue('N2', 'completed');
        $sheet->setCellValue('O2', 'daily');

        // Add sample data - Row 3: Corrective maintenance with downtime
        $sheet->setCellValue('A3', '14/06/2026');
        $sheet->setCellValue('B3', 'Mesin Produksi B1');
        $sheet->setCellValue('C3', 'Line B');
        $sheet->setCellValue('D3', 'on');  // line_status - line ini dihitung downtime-nya
        $sheet->setCellValue('E3', 'corrective');
        $sheet->setCellValue('F3', 'Mechanical');
        $sheet->setCellValue('G3', 'Perbaikan bearing - bearing aus, diganti');
        $sheet->setCellValue('H3', 'Bearing 6203');
        $sheet->setCellValue('I3', '2');
        $sheet->setCellValue('J3', 'Grade A, kondisi normal');
        $sheet->setCellValue('K3', '08:30');
        $sheet->setCellValue('L3', '10:15');
        $sheet->setCellValue('M3', '105');  // minutes
        $sheet->setCellValue('N3', 'completed');
        $sheet->setCellValue('O3', 'daily');

        // Add sample data - Row 4: Line status OFF (tidak dihitung downtime)
        $sheet->setCellValue('A4', '13/06/2026');
        $sheet->setCellValue('B4', 'Mesin Utility');
        $sheet->setCellValue('C4', 'Utility');
        $sheet->setCellValue('D4', 'off');  // line_status - line ini TIDAK dihitung downtime-nya
        $sheet->setCellValue('E4', 'preventive');
        $sheet->setCellValue('F4', 'Utility');
        $sheet->setCellValue('G4', 'Maintenance mesin utility - tidak untuk production');
        $sheet->setCellValue('H4', '');
        $sheet->setCellValue('I4', '');
        $sheet->setCellValue('J4', '');
        $sheet->setCellValue('K4', '');
        $sheet->setCellValue('L4', '');
        $sheet->setCellValue('M4', '');
        $sheet->setCellValue('N4', 'completed');
        $sheet->setCellValue('O4', 'daily');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_laporan_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }

    /**
     * Get line information for a selected machine (AJAX endpoint)
     */
    public function getMachineLineInfo($machineId)
    {
        $machine = Machine::with('line')->find($machineId);
        
        if (!$machine || !$machine->line) {
            return response()->json([
                'success' => false,
                'message' => 'Machine or Line not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'line_id' => $machine->line->id,
            'line_name' => $machine->line->name,
            'machine_name' => $machine->name
        ]);
    }

    private function getDowntimeByScope($tahun, $bulan, $baseQueryCallback = null)
    {
        if ($baseQueryCallback === null) {
            // Default query for given month/year
            $baseQueryCallback = function() use ($tahun, $bulan) {
                return LaporanHarian::whereYear('tanggal_laporan', $tahun)
                    ->whereMonth('tanggal_laporan', $bulan);
            };
        }

        $scopes = ['Electrik', 'Mekanik', 'Utility', 'Building'];
        $downtimeByScope = [];

        foreach ($scopes as $scope) {
            $query = $baseQueryCallback();
            $downtimeMinutes = $query->where('scope', $scope)
                ->whereIn('jenis_pekerjaan', ['corrective', 'preventive', 'change over product'])
                ->sum('downtime_min') ?? 0;
            
            // Convert minutes to hours
            $downtimeHours = round($downtimeMinutes / 60, 2);
            
            $downtimeByScope[] = [
                'scope' => $scope,
                'downtime_hours' => $downtimeHours,
                'downtime_minutes' => $downtimeMinutes
            ];
        }

        return $downtimeByScope;
    }
}
