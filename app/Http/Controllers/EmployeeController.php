<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Imports\EmployeesImport;
use App\Http\Requests\ImportEmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::whereHas('roles', function($q) {
            $q->where('name', 'operator');
        })->paginate(10);

        return view('employee.index', compact('employees'));
    }

    public function create()
    {
        return view('employee.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('operator');

        return redirect()->route('employees.index')->with('success', 'Operator berhasil ditambahkan!');
    }

    public function edit(User $employee)
    {
        // Cek apakah user adalah operator
        if (!$employee->hasRole('operator')) {
            abort(403, 'User ini bukan operator');
        }

        return view('employee.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        // Cek apakah user adalah operator
        if (!$employee->hasRole('operator')) {
            abort(403, 'User ini bukan operator');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $employee->name = $validated['name'];
        $employee->email = $validated['email'];

        if ($request->filled('password')) {
            $employee->password = Hash::make($validated['password']);
        }

        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Operator berhasil diperbarui!');
    }

    public function destroy(User $employee)
    {
        // Cek apakah user adalah operator
        if (!$employee->hasRole('operator')) {
            abort(403, 'User ini bukan operator');
        }

        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Operator berhasil dipecat!');
    }

    public function importForm()
    {
        return view('employee.import');
    }

    public function import(ImportEmployeeRequest $request)
    {
        try {
            $file = $request->file('file');
            $successCount = 0;
            $skipCount = 0;
            $errorMessages = [];

            // Import file
            $rows = Excel::toArray(new EmployeesImport, $file);
            
            if (empty($rows) || empty($rows[0])) {
                return redirect()->route('employees.index')->with('error', 'File Excel kosong atau format tidak sesuai');
            }

            foreach ($rows[0] as $index => $row) {
                try {
                    // Skip header row (sudah di-handle oleh WithHeadingRow)
                    if (empty($row['name']) || empty($row['email'])) {
                        $skipCount++;
                        continue;
                    }

                    // Cek apakah email sudah ada
                    $existingUser = User::where('email', trim($row['email']))->first();
                    if ($existingUser) {
                        $skipCount++;
                        $errorMessages[] = "Baris " . ($index + 2) . ": Email '{$row['email']}' sudah terdaftar";
                        continue;
                    }

                    // Validasi email
                    if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                        $skipCount++;
                        $errorMessages[] = "Baris " . ($index + 2) . ": Format email '{$row['email']}' tidak valid";
                        continue;
                    }

                    // Create user
                    $user = User::create([
                        'name' => trim($row['name']),
                        'email' => trim($row['email']),
                        'password' => Hash::make($row['password'] ?? 'password123'),
                    ]);

                    // Assign role
                    $user->assignRole('operator');

                    $successCount++;
                } catch (\Exception $e) {
                    $skipCount++;
                    $errorMessages[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Import selesai! {$successCount} karyawan berhasil ditambahkan.";
            if ($skipCount > 0) {
                $message .= " {$skipCount} baris dilewati.";
            }

            if (!empty($errorMessages)) {
                $message .= "\n\n" . implode("\n", array_slice($errorMessages, 0, 5));
                if (count($errorMessages) > 5) {
                    $message .= "\n... dan " . (count($errorMessages) - 5) . " error lainnya";
                }
                return redirect()->route('employees.index')->with('warning', $message)->with('errorDetails', $errorMessages);
            }

            return redirect()->route('employees.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('employees.index')->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }

    public function template()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('B1', 'email');
        $sheet->setCellValue('C1', 'password');

        // Format header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        // Set column width
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);

        // Add sample data
        $sheet->setCellValue('A2', 'Budi Santoso');
        $sheet->setCellValue('B2', 'budi@example.com');
        $sheet->setCellValue('C2', 'pass12345');

        $sheet->setCellValue('A3', 'Ani Wijaya');
        $sheet->setCellValue('B3', 'ani@example.com');
        $sheet->setCellValue('C3', 'pass54321');

        // Save
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'template_import_karyawan_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }
}
