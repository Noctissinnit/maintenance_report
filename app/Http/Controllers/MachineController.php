<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Line;
use App\Imports\MachinesImport;
use App\Http\Requests\ImportMachineRequest;
use Illuminate\Http\Request;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MachineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $machines = Machine::with('line')->paginate(10);
        return view('machine.index', compact('machines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lines = Line::where('status', 'active')->get();
        return view('machine.create', compact('lines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:machines',
            'code' => 'nullable|string|max:100',
            'line_id' => 'required|integer|exists:lines,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Machine::create($validated);

        return redirect()->route('machines.index')->with('success', 'Mesin berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Machine $machine)
    {
        $lines = Line::where('status', 'active')->get();
        return view('machine.edit', compact('machine', 'lines'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:machines,name,' . $machine->id,
            'code' => 'nullable|string|max:100',
            'line_id' => 'required|integer|exists:lines,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $machine->update($validated);

        return redirect()->route('machines.index')->with('success', 'Mesin berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Machine $machine)
    {
        $machine->delete();

        return redirect()->route('machines.index')->with('success', 'Mesin berhasil dihapus!');
    }

    /**
     * Import machines from file
     */
    public function importForm()
    {
        return view('machine.import');
    }

    /**
     * Import machines
     */
    public function import(ImportMachineRequest $request)
    {
        try {
            $file = $request->file('file');
            $successCount = 0;
            $skipCount = 0;
            $errorMessages = [];

            $rows = Excel::toArray(new MachinesImport, $file);
            
            if (empty($rows) || empty($rows[0])) {
                return redirect()->route('machines.index')->with('error', 'File Excel kosong atau format tidak sesuai');
            }

            foreach ($rows[0] as $index => $row) {
                try {
                    if (empty($row['name']) || empty($row['line_name'])) {
                        $skipCount++;
                        continue;
                    }

                    $line = Line::where('name', trim($row['line_name']))->first();
                    if (!$line) {
                        $skipCount++;
                        $errorMessages[] = "Baris " . ($index + 2) . ": Line '{$row['line_name']}' tidak ditemukan";
                        continue;
                    }

                    $existingMachine = Machine::where('name', trim($row['name']))->first();
                    if ($existingMachine) {
                        $skipCount++;
                        $errorMessages[] = "Baris " . ($index + 2) . ": Nama mesin '{$row['name']}' sudah terdaftar";
                        continue;
                    }

                    if (!empty($row['code'])) {
                        $existingCode = Machine::where('code', trim($row['code']))->first();
                        if ($existingCode) {
                            $skipCount++;
                            $errorMessages[] = "Baris " . ($index + 2) . ": Kode '{$row['code']}' sudah terdaftar";
                            continue;
                        }
                    }

                    Machine::create([
                        'name' => trim($row['name']),
                        'code' => !empty($row['code']) ? trim($row['code']) : null,
                        'line_id' => $line->id,
                        'description' => !empty($row['description']) ? trim($row['description']) : null,
                        'status' => strtolower($row['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $skipCount++;
                    $errorMessages[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Import selesai! {$successCount} mesin berhasil ditambahkan.";
            if ($skipCount > 0) {
                $message .= " {$skipCount} baris dilewati.";
            }

            if (!empty($errorMessages)) {
                $message .= "\n\n" . implode("\n", array_slice($errorMessages, 0, 5));
                if (count($errorMessages) > 5) {
                    $message .= "\n... dan " . (count($errorMessages) - 5) . " error lainnya";
                }
            }

            return redirect()->route('machines.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('machines.index')->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }

    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('B1', 'code');
        $sheet->setCellValue('C1', 'line_name');
        $sheet->setCellValue('D1', 'description');
        $sheet->setCellValue('E1', 'status');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(15);

        $sheet->setCellValue('A2', 'Mesin Produksi A1');
        $sheet->setCellValue('B2', 'MPA001');
        $sheet->setCellValue('C2', 'Line A');
        $sheet->setCellValue('D2', 'Mesin utama line A');
        $sheet->setCellValue('E2', 'active');

        $sheet->setCellValue('A3', 'Mesin Produksi B1');
        $sheet->setCellValue('B3', 'MPB001');
        $sheet->setCellValue('C3', 'Line B');
        $sheet->setCellValue('D3', 'Mesin utama line B');
        $sheet->setCellValue('E3', 'active');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_machine_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }
}
