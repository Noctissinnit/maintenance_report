<?php

namespace App\Http\Controllers;

use App\Models\LaporanHarian;
use App\Models\Machine;
use App\Models\SparePart;
use App\Models\Line;
use App\Http\Requests\ImportLaporanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Get search parameters
        $search = $request->input('search', '');
        $mesin_filter = $request->input('mesin', '');
        $line_filter = $request->input('line', '');
        $jenis_filter = $request->input('jenis_pekerjaan', '');
        $tipe_filter = $request->input('tipe_laporan', '');
        $start_date = $request->input('start_date', '');
        $end_date = $request->input('end_date', '');

        // Base query
        $query = LaporanHarian::select(
                'id',
                'user_id',
                'machine_id',
                'line_id',
                'mesin_name',
                'line',
                'jenis_pekerjaan',
                'tipe_laporan',
                'downtime_min',
                'tanggal_laporan'
            )
            ->with([
                'machine:id,name',
                'line:id,name'
            ])
            ->orderBy('tanggal_laporan', 'desc');

        // Jika bukan admin → hanya lihat laporan sendiri
        if (!Auth::user()->hasRole('admin')) {
            $query->where('user_id', Auth::id());
        }

        // Apply search filters
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('mesin_name', 'like', '%' . $search . '%')
                  ->orWhere('line', 'like', '%' . $search . '%')
                  ->orWhere('catatan', 'like', '%' . $search . '%')
                  ->orWhere('jenis_pekerjaan', 'like', '%' . $search . '%');
            });
        }

        // Filter by mesin
        if (!empty($mesin_filter)) {
            $query->where('mesin_name', 'like', '%' . $mesin_filter . '%');
        }

        // Filter by line
        if (!empty($line_filter)) {
            $query->where('line', 'like', '%' . $line_filter . '%');
        }

        // Filter by jenis pekerjaan
        if (!empty($jenis_filter)) {
            $query->where('jenis_pekerjaan', $jenis_filter);
        }

        // Filter by tipe laporan
        if (!empty($tipe_filter)) {
            $query->where('tipe_laporan', $tipe_filter);
        }

        // Filter by date range
        if (!empty($start_date)) {
            $query->where('tanggal_laporan', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $query->where('tanggal_laporan', '<=', $end_date);
        }

        // Eksekusi query - gunakan 15 items per page
        $laporan = $query->paginate(15)->appends($request->query());

        return view('laporan.index', compact('laporan', 'search', 'mesin_filter', 'line_filter', 'jenis_filter', 'tipe_filter', 'start_date', 'end_date'));
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

        $validated = $request->validate([
            'machine_id' => 'integer|exists:machines,id',
            'line_id' => 'required|integer|exists:lines,id',
            'catatan' => 'nullable|string',
            'spare_part_id' => 'nullable|integer|exists:spare_parts,id',
            'qty_sparepart' => 'integer|min:0',
            'komentar_sparepart' => 'nullable|string',
            'jenis_pekerjaan' => 'required|in:corrective,preventive,modifikasi,utility',
            'scope' => 'required|in:Electrik,Mekanik,Utility,Building',
            'start_time' => 'nullable|date_format:Y-m-d\TH:i',
            'end_time' => 'nullable|date_format:Y-m-d\TH:i|after:start_time',
            'downtime_min' => 'integer|min:0',
            'tipe_laporan' => 'in:harian,mingguan,bulanan',
            'tanggal_laporan' => 'required|date',
        ]);

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

        // Calculate downtime for corrective and preventive types
        if (($validated['jenis_pekerjaan'] === 'corrective' || $validated['jenis_pekerjaan'] === 'preventive') && isset($validated['start_time']) && isset($validated['end_time'])) {
            $start = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['start_time']);
            $end = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['end_time']);
            $validated['downtime_min'] = (int) $start->diffInMinutes($end);
        } else {
            $validated['downtime_min'] = 0;
        }
        
        LaporanHarian::create($validated);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

        $validated = $request->validate([
            'machine_id' => 'integer|exists:machines,id',
            'line_id' => 'required|integer|exists:lines,id',
            'catatan' => 'nullable|string',
            'spare_part_id' => 'nullable|integer|exists:spare_parts,id',
            'qty_sparepart' => 'integer|min:0',
            'komentar_sparepart' => 'nullable|string',
            'jenis_pekerjaan' => 'required|in:corrective,preventive,modifikasi,utility',
            'scope' => 'required|in:Electrik,Mekanik,Utility,Building',
            'start_time' => 'nullable|date_format:Y-m-d\TH:i',
            'end_time' => 'nullable|date_format:Y-m-d\TH:i|after:start_time',
            'downtime_min' => 'integer|min:0',
            'tipe_laporan' => 'in:harian,mingguan,bulanan',
            'tanggal_laporan' => 'required|date',
        ]);

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

        // Calculate downtime for corrective and preventive types
        if (($validated['jenis_pekerjaan'] === 'corrective' || $validated['jenis_pekerjaan'] === 'preventive') && isset($validated['start_time']) && isset($validated['end_time'])) {
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
        $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);

        // Set column width
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(12);
        $sheet->getColumnDimension('K')->setWidth(12);
        $sheet->getColumnDimension('L')->setWidth(12);
        $sheet->getColumnDimension('M')->setWidth(12);
        $sheet->getColumnDimension('N')->setWidth(12);

        // Add sample data
        $sheet->setCellValue('A2', '15/02/2026');
        $sheet->setCellValue('B2', 'Mesin Produksi A1');
        $sheet->setCellValue('C2', 'Line A');
        $sheet->setCellValue('D2', 'preventive');
        $sheet->setCellValue('E2', 'Pembersihan dan pelumasan');
        $sheet->setCellValue('F2', 'Rutin harian');
        $sheet->setCellValue('G2', '');
        $sheet->setCellValue('H2', '');
        $sheet->setCellValue('I2', '');
        $sheet->setCellValue('J2', '');
        $sheet->setCellValue('K2', '');
        $sheet->setCellValue('L2', '');
        $sheet->setCellValue('M2', 'completed');
        $sheet->setCellValue('N2', 'daily');

        $sheet->setCellValue('A3', '14/02/2026');
        $sheet->setCellValue('B3', 'Mesin Produksi B1');
        $sheet->setCellValue('C3', 'Line B');
        $sheet->setCellValue('D3', 'corrective');
        $sheet->setCellValue('E3', 'Perbaikan bearing');
        $sheet->setCellValue('F3', 'Bearing aus, diganti');
        $sheet->setCellValue('G3', 'Bearing 6203');
        $sheet->setCellValue('H3', '2');
        $sheet->setCellValue('I3', 'Grade A');
        $sheet->setCellValue('J3', '08:30');
        $sheet->setCellValue('K3', '10:15');
        $sheet->setCellValue('L3', '105');
        $sheet->setCellValue('M3', 'completed');
        $sheet->setCellValue('N3', 'daily');

        // Add data validation hints in row 1 (comment)
        $sheet->getCell('D1')->setValue('jenis_pekerjaan (preventive/corrective)');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_laporan_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }
}
