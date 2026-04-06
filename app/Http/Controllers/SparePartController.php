<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use App\Models\LaporanHarian;
use App\Http\Requests\ImportSparePartRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SparePartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $spareParts = SparePart::paginate(10);
        return view('sparepart.index', compact('spareParts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sparepart.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:spare_parts',
            'code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        SparePart::create($validated);

        return redirect()->route('spare-parts.index')->with('success', 'Spare Part berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SparePart $sparePart)
    {
        return view('sparepart.edit', compact('sparePart'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SparePart $sparePart)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:spare_parts,name,' . $sparePart->id,
            'code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $sparePart->update($validated);

        return redirect()->route('spare-parts.index')->with('success', 'Spare Part berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SparePart $sparePart)
    {
        $sparePart->delete();

        return redirect()->route('spare-parts.index')->with('success', 'Spare Part berhasil dihapus!');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('sparepart.import');
    }

    /**
     * Import spare parts
     */
    public function import(ImportSparePartRequest $request)
    {
        try {
            $file = $request->file('file');
            $successCount = 0;
            $skipCount = 0;
            $errorMessages = [];

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
                    // First row is header
                    $headerRow = array_map('strtolower', array_map('trim', $rowData));
                } else {
                    // Subsequent rows are data
                    if (!empty(array_filter($rowData))) { // Skip empty rows
                        $rows[] = array_combine($headerRow, $rowData);
                    }
                }
            }
            
            if (empty($rows)) {
                return redirect()->route('spare-parts.index')->with('error', 'File Excel kosong atau format tidak sesuai');
            }

            foreach ($rows as $index => $row) {
                try {
                    if (empty($row['name'])) {
                        $skipCount++;
                        continue;
                    }

                    $existingSparePart = SparePart::where('name', trim($row['name']))->first();
                    if ($existingSparePart) {
                        $skipCount++;
                        $errorMessages[] = "Baris " . ($index + 2) . ": Nama spare part '{$row['name']}' sudah terdaftar";
                        continue;
                    }

                    if (!empty($row['code'])) {
                        $existingCode = SparePart::where('code', trim($row['code']))->first();
                        if ($existingCode) {
                            $skipCount++;
                            $errorMessages[] = "Baris " . ($index + 2) . ": Kode '{$row['code']}' sudah terdaftar";
                            continue;
                        }
                    }

                    SparePart::create([
                        'name' => trim($row['name']),
                        'code' => !empty($row['code']) ? trim($row['code']) : null,
                        'description' => !empty($row['description']) ? trim($row['description']) : null,
                        'category' => !empty($row['category']) ? trim($row['category']) : null,
                        'stock' => !empty($row['stock']) ? (float)$row['stock'] : 0,
                        'unit' => !empty($row['unit']) ? trim($row['unit']) : 'pcs',
                        'notes' => !empty($row['notes']) ? trim($row['notes']) : null,
                        'status' => strtolower($row['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $skipCount++;
                    $errorMessages[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Import selesai! {$successCount} spare part berhasil ditambahkan.";
            if ($skipCount > 0) {
                $message .= " {$skipCount} baris dilewati.";
            }

            if (!empty($errorMessages)) {
                $message .= "\n\n" . implode("\n", array_slice($errorMessages, 0, 5));
                if (count($errorMessages) > 5) {
                    $message .= "\n... dan " . (count($errorMessages) - 5) . " error lainnya";
                }
            }

            return redirect()->route('spare-parts.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('spare-parts.index')->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }

    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('B1', 'code');
        $sheet->setCellValue('C1', 'category');
        $sheet->setCellValue('D1', 'stock');
        $sheet->setCellValue('E1', 'unit');
        $sheet->setCellValue('F1', 'description');
        $sheet->setCellValue('G1', 'notes');
        $sheet->setCellValue('H1', 'status');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(12);

        $sheet->setCellValue('A2', 'Bearing 6203');
        $sheet->setCellValue('B2', 'BP001');
        $sheet->setCellValue('C2', 'Bearing');
        $sheet->setCellValue('D2', 50);
        $sheet->setCellValue('E2', 'pcs');
        $sheet->setCellValue('F2', 'Bearing standard untuk motor');
        $sheet->setCellValue('G2', 'Stok minimal 30 pcs');
        $sheet->setCellValue('H2', 'active');

        $sheet->setCellValue('A3', 'Belt Konveyor');
        $sheet->setCellValue('B3', 'BK001');
        $sheet->setCellValue('C3', 'Belt');
        $sheet->setCellValue('D3', 10);
        $sheet->setCellValue('E3', 'meter');
        $sheet->setCellValue('F3', 'Belt konveyor ukuran standar');
        $sheet->setCellValue('G3', 'Stok minimal 5 meter');
        $sheet->setCellValue('H3', 'active');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_sparepart_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }

    /**
     * Monitoring usage
     */
    public function monitoring(Request $request)
    {
        // Get filter parameters
        $bulan = $request->get('bulan') ?? now()->month;
        $tahun = $request->get('tahun') ?? now()->year;

        $stockAvailable = SparePart::sum('stock');


        // Get spare parts usage for selected month
        $usage = LaporanHarian::select(
                'laporan_harian.sparepart',
                DB::raw('SUM(laporan_harian.qty_sparepart) as total_qty'),
                'spare_parts.stock as stock_awal'
            )
            ->leftJoin('spare_parts', 'spare_parts.name', '=', 'laporan_harian.sparepart')
            ->whereYear('laporan_harian.created_at', $tahun)
            ->whereMonth('laporan_harian.created_at', $bulan)
            ->whereNotNull('laporan_harian.sparepart')
            ->where('laporan_harian.sparepart', '<>', '')
            ->groupBy('laporan_harian.sparepart', 'spare_parts.stock')
            ->orderByDesc('total_qty')
            ->get();

          
        $totalUsage = $usage->sum('total_qty');


        $availableMonths = LaporanHarian::select(DB::raw('MONTH(created_at) as bulan'))
            ->distinct()
            ->orderBy('bulan')
            ->pluck('bulan');

        return view('sparepart.monitoring', compact(
            'usage',
            'bulan',
            'tahun',
            'totalUsage',
            'availableMonths',
            'stockAvailable'
        ));
    }

    /**
     * Clear all spare parts data
     */
    public function clearAll()
    {
        try {
            $count = SparePart::count();
            
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Delete laporan first (child table), then spare parts
            LaporanHarian::where(DB::raw('spare_part_id IS NOT NULL'))->delete();
            SparePart::truncate();
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            return redirect()->route('spare-parts.index')->with('success', "$count data spare part berhasil dihapus!");
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return redirect()->route('spare-parts.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}

