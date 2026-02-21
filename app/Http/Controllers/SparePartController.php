<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use App\Models\LaporanHarian;
use App\Imports\SparePartsImport;
use App\Http\Requests\ImportSparePartRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
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

            $rows = Excel::toArray(new SparePartsImport, $file);
            
            if (empty($rows) || empty($rows[0])) {
                return redirect()->route('spare-parts.index')->with('error', 'File Excel kosong atau format tidak sesuai');
            }

            foreach ($rows[0] as $index => $row) {
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
        $sheet->setCellValue('C1', 'description');
        $sheet->setCellValue('D1', 'category');
        $sheet->setCellValue('E1', 'status');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(15);

        $sheet->setCellValue('A2', 'Bearing 6203');
        $sheet->setCellValue('B2', 'BP001');
        $sheet->setCellValue('C2', 'Bearing standard untuk motor');
        $sheet->setCellValue('D2', 'Bearing');
        $sheet->setCellValue('E2', 'active');

        $sheet->setCellValue('A3', 'Belt Konveyor');
        $sheet->setCellValue('B3', 'BK001');
        $sheet->setCellValue('C3', 'Belt konveyor ukuran standar');
        $sheet->setCellValue('D3', 'Belt');
        $sheet->setCellValue('E3', 'active');

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

        // Get spare parts usage for selected month
        $usage = LaporanHarian::select('sparepart', DB::raw('SUM(qty_sparepart) as total_qty'))
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->whereNotNull('sparepart')
            ->where('sparepart', '<>', '')
            ->groupBy('sparepart')
            ->orderByDesc('total_qty')
            ->get();

        // Get total spare parts count
        $totalUsage = $usage->sum('total_qty');

        // Get all available months with data
        $availableMonths = LaporanHarian::select(DB::raw('MONTH(created_at) as bulan'))
            ->distinct()
            ->orderBy('bulan')
            ->pluck('bulan');

        return view('sparepart.monitoring', compact(
            'usage',
            'bulan',
            'tahun',
            'totalUsage',
            'availableMonths'
        ));
    }
}

