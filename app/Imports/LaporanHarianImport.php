<?php

namespace App\Imports;

use App\Models\LaporanHarian;
use App\Models\Machine;
use App\Models\Line;
use App\Models\SparePart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LaporanHarianImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     * @return LaporanHarian|null
     */
    public function model(array $row)
    {
        // Skip baris kosong
        if (empty($row['tanggal_laporan']) && empty($row['machine_name'])) {
            return null;
        }

        // Get machine by name
        $machine = null;
        $line = null;
        $sparePart = null;

        if (!empty($row['machine_name'])) {
            $machine = Machine::where('name', trim($row['machine_name']))->first();
            if (!$machine) {
                throw new \Exception("Mesin '{$row['machine_name']}' tidak ditemukan");
            }
            $line = $machine->line;
        }

        if (!empty($row['line_name'])) {
            $line = Line::where('name', trim($row['line_name']))->first();
            if (!$line) {
                throw new \Exception("Line '{$row['line_name']}' tidak ditemukan");
            }
        }

        if (!empty($row['spare_part_name'])) {
            $sparePart = SparePart::where('name', trim($row['spare_part_name']))->first();
            if (!$sparePart) {
                throw new \Exception("Spare Part '{$row['spare_part_name']}' tidak ditemukan");
            }
        }

        // Parse tanggal
        $tanggalLaporan = null;
        if (!empty($row['tanggal_laporan'])) {
            try {
                $dateValue = $row['tanggal_laporan'];
                
                // Jika tanggal dari Excel (bisa angka atau string)
                if (is_numeric($dateValue)) {
                    // Excel serial date
                    $tanggalLaporan = \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue))->toDateString();
                } else {
                    // Coba berbagai format string
                    $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'd.m.Y', 'Y/m/d'];
                    $parsed = false;
                    
                    foreach ($formats as $format) {
                        try {
                            $tanggalLaporan = \Carbon\Carbon::createFromFormat($format, trim($dateValue))->toDateString();
                            $parsed = true;
                            break;
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                    
                    if (!$parsed) {
                        throw new \Exception("Format tanggal tidak valid: {$dateValue}");
                    }
                }
            } catch (\Exception $e) {
                throw new \Exception("Format tanggal tidak valid: {$row['tanggal_laporan']}. " . $e->getMessage());
            }
        }

        // Parse start time dan end time
        $startTime = null;
        $endTime = null;
        $jenisPekerjaan = strtolower($row['jenis_pekerjaan'] ?? 'preventive');

        if ($jenisPekerjaan === 'corrective') {
            if (!empty($row['start_time'])) {
                $startTime = $this->parseTimeValue($row['start_time']);
            }

            if (!empty($row['end_time'])) {
                $endTime = $this->parseTimeValue($row['end_time']);
            }
        }

        $laporanHarian = new LaporanHarian([
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

        return $laporanHarian;
    }

    public function rules(): array
    {
        return [
            'tanggal_laporan' => 'required|string',
            'machine_name' => 'nullable|string|max:255',
            'line_name' => 'nullable|string|max:255',
            'spare_part_name' => 'nullable|string|max:255',
            'qty_spare_part' => 'nullable|numeric|min:0',
            'jenis_pekerjaan' => 'nullable|in:preventive,corrective',
            'scope' => 'nullable|string',
            'notes' => 'nullable|string',
            'spare_part_notes' => 'nullable|string',
            'status' => 'nullable|in:completed,pending',
            'report_type' => 'nullable|in:daily,weekly,monthly',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'downtime_min' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Parse time value dari berbagai format Excel
     */
    private function parseTimeValue($timeValue)
    {
        if (empty($timeValue)) {
            return null;
        }

        try {
            $timeValue = trim($timeValue);
            
            // Jika numeric (Excel serial time)
            if (is_numeric($timeValue)) {
                // Excel stores time as decimal fraction of a day
                $hours = floor($timeValue * 24);
                $minutes = floor(($timeValue * 24 - $hours) * 60);
                $seconds = floor((($timeValue * 24 - $hours) * 60 - $minutes) * 60);
                
                return \Carbon\Carbon::createFromFormat('H:i:s', sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds));
            }
            
            // Ganti separator yang salah
            // Tangani format seperti "14.50,00" atau "16.42,00" (European format)
            $timeValue = str_replace(',', ':', $timeValue);
            // Tangani format seperti "00.1.00" atau "10.00.0" - ganti titik dengan colon
            $timeValue = str_replace('.', ':', $timeValue);
            
            // Coba berbagai format string
            $formats = ['H:i:s', 'H:i', 'H'];
            
            foreach ($formats as $format) {
                try {
                    return \Carbon\Carbon::createFromFormat($format, $timeValue);
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            // Jika semua format gagal, skip dengan returning null
            Log::warning("Format waktu tidak dapat diparsing: {$timeValue}");
            return null;
            
        } catch (\Exception $e) {
            Log::warning("Error parsing waktu: {$timeValue}, Error: " . $e->getMessage());
            return null;
        }
    }
}
