<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TemplateExportController extends Controller
{
    /**
     * Download template for machines
     */
    public function downloadMachineTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mesin');

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        // Headers
        $headers = ['Nama Mesin', 'Line', 'Deskripsi', 'Status'];
        foreach ($headers as $key => $header) {
            $column = chr(65 + $key); // A, B, C, D
            $sheet->setCellValue($column . '1', $header);
        }
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Sample data
        $sampleData = [
            ['Mesin Pembanding Dimensi', 'Inspection', 'Mesin untuk inspeksi dimensi', 'active'],
            ['Motor Penggerak', 'Assembly', 'Motor penggerak produksi', 'active'],
        ];

        foreach ($sampleData as $rowKey => $rowData) {
            foreach ($rowData as $colKey => $value) {
                $column = chr(65 + $colKey); // A, B, C, D
                $sheet->setCellValue($column . ($rowKey + 2), $value);
            }
        }

        // Adjust column widths
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(12);

        // Generate file
        $fileName = 'template_mesin_' . date('Y-m-d') . '.xlsx';
        return $this->downloadExcel($spreadsheet, $fileName);
    }

    /**
     * Download template for lines
     */
    public function downloadLineTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Line');

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        // Headers
        $headers = ['Nama Line', 'Deskripsi', 'Status'];
        foreach ($headers as $key => $header) {
            $column = chr(65 + $key); // A, B, C
            $sheet->setCellValue($column . '1', $header);
        }
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        // Sample data
        $sampleData = [
            ['Inspection', 'Departemen inspeksi kualitas', 'active'],
            ['Assembly', 'Departemen perakitan produk', 'active'],
            ['Packing', 'Departemen pengemasan produk', 'active'],
        ];

        foreach ($sampleData as $rowKey => $rowData) {
            foreach ($rowData as $colKey => $value) {
                $column = chr(65 + $colKey); // A, B, C
                $sheet->setCellValue($column . ($rowKey + 2), $value);
            }
        }

        // Adjust column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(12);

        // Generate file
        $fileName = 'template_line_' . date('Y-m-d') . '.xlsx';
        return $this->downloadExcel($spreadsheet, $fileName);
    }

    /**
     * Download template for spare parts
     */
    public function downloadSparePartTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Spare Part');

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        // Headers
        $headers = ['Nama Spare Part', 'Kode', 'Deskripsi', 'Stok', 'Status'];
        foreach ($headers as $key => $header) {
            $column = chr(65 + $key); // A, B, C, D, E
            $sheet->setCellValue($column . '1', $header);
        }
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Sample data
        $sampleData = [
            ['V-Belt Taper Lock', 'VBT-001', 'Sabuk transmisi V-Belt', '10', 'active'],
            ['Bearing SKF', 'BRG-002', 'Bearing standar industri', '15', 'active'],
            ['Motor Elektrik', 'MTR-003', 'Motor 3 phase 2.2kW', '5', 'active'],
        ];

        foreach ($sampleData as $rowKey => $rowData) {
            foreach ($rowData as $colKey => $value) {
                $column = chr(65 + $colKey); // A, B, C, D, E
                $sheet->setCellValue($column . ($rowKey + 2), $value);
            }
        }

        // Adjust column widths
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(12);

        // Generate file
        $fileName = 'template_spare_part_' . date('Y-m-d') . '.xlsx';
        return $this->downloadExcel($spreadsheet, $fileName);
    }

    /**
     * Helper method to download Excel file
     */
    private function downloadExcel(Spreadsheet $spreadsheet, $fileName)
    {
        $writer = new Xlsx($spreadsheet);

        // Create temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');
        $writer->save($tempFile);

        // Read file content
        $fileContent = file_get_contents($tempFile);

        // Delete temporary file
        unlink($tempFile);

        return response($fileContent, 200)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}

