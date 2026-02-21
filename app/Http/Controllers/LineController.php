<?php

namespace App\Http\Controllers;

use App\Models\Line;
use App\Imports\LinesImport;
use App\Http\Requests\ImportLineRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lines = Line::paginate(10);
        return view('line.index', compact('lines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('line.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:lines',
            'code' => 'nullable|string|max:100|unique:lines',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Line::create($validated);

        return redirect()->route('lines.index')->with('success', 'Line berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Line $line)
    {
        return view('line.edit', compact('line'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Line $line)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:lines,name,' . $line->id,
            'code' => 'nullable|string|max:100|unique:lines,code,' . $line->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $line->update($validated);

        return redirect()->route('lines.index')->with('success', 'Line berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Line $line)
    {
        $line->delete();

        return redirect()->route('lines.index')->with('success', 'Line berhasil dihapus!');
    }

    public function importForm()
    {
        return view('line.import');
    }

    public function import(ImportLineRequest $request)
    {
        try {
            $file = $request->file('file');
            $successCount = 0;
            $skipCount = 0;
            $errorMessages = [];

            $rows = Excel::toArray(new LinesImport, $file);
            
            if (empty($rows) || empty($rows[0])) {
                return redirect()->route('lines.index')->with('error', 'File Excel kosong atau format tidak sesuai');
            }

            foreach ($rows[0] as $index => $row) {
                try {
                    if (empty($row['name'])) {
                        $skipCount++;
                        continue;
                    }

                    $existingLine = Line::where('name', trim($row['name']))->first();
                    if ($existingLine) {
                        $skipCount++;
                        $errorMessages[] = "Baris " . ($index + 2) . ": Nama line '{$row['name']}' sudah terdaftar";
                        continue;
                    }

                    if (!empty($row['code'])) {
                        $existingCode = Line::where('code', trim($row['code']))->first();
                        if ($existingCode) {
                            $skipCount++;
                            $errorMessages[] = "Baris " . ($index + 2) . ": Kode '{$row['code']}' sudah terdaftar";
                            continue;
                        }
                    }

                    Line::create([
                        'name' => trim($row['name']),
                        'code' => !empty($row['code']) ? trim($row['code']) : null,
                        'description' => !empty($row['description']) ? trim($row['description']) : null,
                        'status' => strtolower($row['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $skipCount++;
                    $errorMessages[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Import selesai! {$successCount} line berhasil ditambahkan.";
            if ($skipCount > 0) {
                $message .= " {$skipCount} baris dilewati.";
            }

            if (!empty($errorMessages)) {
                $message .= "\n\n" . implode("\n", array_slice($errorMessages, 0, 5));
                if (count($errorMessages) > 5) {
                    $message .= "\n... dan " . (count($errorMessages) - 5) . " error lainnya";
                }
            }

            return redirect()->route('lines.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('lines.index')->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }

    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('B1', 'code');
        $sheet->setCellValue('C1', 'description');
        $sheet->setCellValue('D1', 'status');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);

        $sheet->setCellValue('A2', 'Line A');
        $sheet->setCellValue('B2', 'LA001');
        $sheet->setCellValue('C2', 'Lini Produksi A');
        $sheet->setCellValue('D2', 'active');

        $sheet->setCellValue('A3', 'Line B');
        $sheet->setCellValue('B3', 'LB001');
        $sheet->setCellValue('C3', 'Lini Produksi B');
        $sheet->setCellValue('D3', 'active');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_line_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }
}
