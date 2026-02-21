<?php

namespace App\Http\Controllers;

use App\Models\LaporanHarian;
use App\Models\Machine;
use App\Models\SparePart;
use App\Models\Line;
use App\Imports\LaporanHarianImport;
use App\Http\Requests\ImportLaporanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanHarianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Cek permission
        if (!Auth::user()->can('view_own_laporan')) {
            abort(403, 'Unauthorized');
        }

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

        // Eksekusi query
        $laporan = $query->paginate(10);

        return view('laporan.index', compact('laporan'));
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

            $rows = Excel::toArray(new LaporanHarianImport, $file);
            
            if (empty($rows) || empty($rows[0])) {
                return redirect()->route('laporan.index')->with('error', 'File Excel kosong atau format tidak sesuai');
            }

            foreach ($rows[0] as $index => $row) {
                try {
                    if (empty($row['tanggal_laporan']) && empty($row['machine_name'])) {
                        $skipCount++;
                        continue;
                    }

                    // Get machine by name
                    $machine = null;
                    $line = null;
                    $sparePart = null;

                    if (!empty($row['machine_name'])) {
                        $machine = Machine::where('name', trim($row['machine_name']))->first();
                        if (!$machine) {
                            $skipCount++;
                            $errorMessages[] = "Baris " . ($index + 2) . ": Mesin '{$row['machine_name']}' tidak ditemukan";
                            continue;
                        }
                        $line = $machine->line;
                    }

                    if (!empty($row['line_name']) && !$line) {
                        $line = Line::where('name', trim($row['line_name']))->first();
                        if (!$line) {
                            $skipCount++;
                            $errorMessages[] = "Baris " . ($index + 2) . ": Line '{$row['line_name']}' tidak ditemukan";
                            continue;
                        }
                    }

                    if (!empty($row['spare_part_name'])) {
                        $sparePart = SparePart::where('name', trim($row['spare_part_name']))->first();
                        if (!$sparePart) {
                            $skipCount++;
                            $errorMessages[] = "Baris " . ($index + 2) . ": Spare Part '{$row['spare_part_name']}' tidak ditemukan";
                            continue;
                        }
                    }

                    // Parse tanggal
                    $tanggalLaporan = null;
                    if (!empty($row['tanggal_laporan'])) {
                        try {
                            $tanggalLaporan = \Carbon\Carbon::createFromFormat('d/m/Y', $row['tanggal_laporan'])->toDateString();
                        } catch (\Exception $e) {
                            $skipCount++;
                            $errorMessages[] = "Baris " . ($index + 2) . ": Format tanggal tidak valid: {$row['tanggal_laporan']}";
                            continue;
                        }
                    }

                    // Parse start time dan end time
                    $startTime = null;
                    $endTime = null;
                    $jenisPekerjaan = strtolower($row['jenis_pekerjaan'] ?? 'preventive');

                    if ($jenisPekerjaan === 'corrective') {
                        if (!empty($row['start_time'])) {
                            try {
                                $startTime = \Carbon\Carbon::createFromFormat('H:i', $row['start_time']);
                            } catch (\Exception $e) {
                                $skipCount++;
                                $errorMessages[] = "Baris " . ($index + 2) . ": Format start_time tidak valid: {$row['start_time']}";
                                continue;
                            }
                        }

                        if (!empty($row['end_time'])) {
                            try {
                                $endTime = \Carbon\Carbon::createFromFormat('H:i', $row['end_time']);
                            } catch (\Exception $e) {
                                $skipCount++;
                                $errorMessages[] = "Baris " . ($index + 2) . ": Format end_time tidak valid: {$row['end_time']}";
                                continue;
                            }
                        }
                    }

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
                        'status' => strtolower($row['status'] ?? 'completed') === 'pending' ? 'pending' : 'completed',
                        'jenis_pekerjaan' => $jenisPekerjaan,
                        'scope' => trim($row['scope'] ?? ''),
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'downtime_min' => !empty($row['downtime_min']) ? (int)$row['downtime_min'] : 0,
                        'tipe_laporan' => strtolower($row['report_type'] ?? 'daily'),
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

            if (!empty($errorMessages)) {
                $message .= "\n\n" . implode("\n", array_slice($errorMessages, 0, 5));
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
