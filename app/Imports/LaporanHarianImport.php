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
        $downtimeMin = 0;
        $jenisPekerjaan = strtolower($row['jenis_pekerjaan'] ?? 'preventive');

        // Parse start_time dan end_time untuk corrective, preventive, dan change over product
        // Pass tanggalLaporan date so times get combined with correct date
        if (in_array($jenisPekerjaan, ['corrective', 'preventive', 'change over product'])) {
            if (!empty($row['start_time'])) {
                $startTime = $this->parseTimeValue($row['start_time'], $tanggalLaporan);
            }

            if (!empty($row['end_time'])) {
                $endTime = $this->parseTimeValue($row['end_time'], $tanggalLaporan);
            }

            // Calculate downtime in minutes from start_time and end_time
            if ($startTime && $endTime) {
                // Use absolute value since downtime is always positive
                $downtimeMin = abs($endTime->diffInMinutes($startTime));
            } elseif (!empty($row['downtime_min'])) {
                // Fallback ke downtime_min dari Excel jika ada
                $downtimeMin = abs((int)$row['downtime_min']);
            }
        } else {
            // Untuk jenis pekerjaan lainnya, gunakan nilai dari Excel
            $downtimeMin = !empty($row['downtime_min']) ? abs((int)$row['downtime_min']) : 0;
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
            'downtime_min' => $downtimeMin,
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
            'jenis_pekerjaan' => 'nullable|in:preventive,corrective,change over product',
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
     * @param $timeValue - nilai waktu dari Excel
     * @param $dateString - tanggal untuk digabungkan dengan waktu (optional, default today)
     */
    private function parseTimeValue($timeValue, $dateString = null)
    {
        if (empty($timeValue)) {
            return null;
        }

        // Default ke tanggal hari ini jika tidak diberikan
        if (!$dateString) {
            $dateString = \Carbon\Carbon::now()->toDateString();
        }

        try {
            $timeValue = trim($timeValue);
            $parsedTime = null;
            
            // Jika numeric (Excel serial time)
            if (is_numeric($timeValue)) {
                $numValue = floatval($timeValue);
                
                // Validasi: Excel time harus dalam range 0-1 (fraction of day)
                // Jika > 1, mungkin itu salah format (e.g. total minutes atau seconds)
                // Silahkan lewat dari parsing, return null
                if ($numValue > 1) {
                    Log::warning("Format waktu numeric terlalu besar: {$timeValue}");
                    return null;
                }
                
                // Excel stores time as decimal fraction of a day (0 = 00:00, 1 = 24:00)
                $hours = floor($numValue * 24);
                $minutes = floor(($numValue * 24 - $hours) * 60);
                $seconds = floor((($numValue * 24 - $hours) * 60 - $minutes) * 60);
                $timeFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                $parsedTime = \Carbon\Carbon::createFromFormat('H:i:s', $timeFormatted);
            } else {
                // Ganti separator yang salah
                // Tangani format seperti "14.50,00" atau "16.42,00" (European format with comma)
                $timeValue = str_replace(',', ':', $timeValue);
                
                // Tangani format seperti "00.1.00" atau "10.00.0" - hanya untuk format yang terlihat seperti jam:menit:detik
                // Cek dulu jumlah bagian
                $parts = explode('.', $timeValue);
                if (count($parts) === 3) {
                    // Kemungkinan format HH.MM.SS - ganti titik dengan colon
                    $timeValue = str_replace('.', ':', $timeValue);
                } elseif (count($parts) > 3) {
                    // Format invalid dengan terlalu banyak titik, skip
                    Log::warning("Format waktu invalid (terlalu banyak separator): {$timeValue}");
                    return null;
                }
                
                // Validate format - harus berupa HH:MM atau HH:MM:SS dengan angka valid
                if (!preg_match('/^([0-9]{1,2}):([0-9]{1,2})(?::([0-9]{1,2}))?$/', $timeValue, $matches)) {
                    Log::warning("Format waktu tidak valid: {$timeValue}");
                    return null;
                }
                
                // Validate range
                $hours = intval($matches[1]);
                $minutes = intval($matches[2]);
                $seconds = isset($matches[3]) ? intval($matches[3]) : 0;
                
                if ($hours > 23 || $minutes > 59 || $seconds > 59) {
                    Log::warning("Format waktu out of range: {$timeValue} (H:{$hours} M:{$minutes} S:{$seconds})");
                    return null;
                }
                
                // Coba berbagai format string dengan validated value
                $formats = ['H:i:s', 'H:i'];
                
                foreach ($formats as $format) {
                    try {
                        $parsedTime = \Carbon\Carbon::createFromFormat($format, $timeValue);
                        break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                
                if (!$parsedTime) {
                    Log::warning("Format waktu tidak dapat diparsing meskipun format valid: {$timeValue}");
                    return null;
                }
            }
            
            // Gabungkan dengan tanggal yang diberikan
            if ($parsedTime) {
                $dateObj = \Carbon\Carbon::createFromFormat('Y-m-d', $dateString);
                return $dateObj->setTimeFromTimeString($parsedTime->format('H:i:s'));
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::warning("Error parsing waktu: {$timeValue}, Error: " . $e->getMessage());
            return null;
        }
    }
}
