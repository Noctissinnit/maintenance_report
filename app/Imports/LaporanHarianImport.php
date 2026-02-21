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
                $tanggalLaporan = \Carbon\Carbon::createFromFormat('d/m/Y', $row['tanggal_laporan'])->toDateString();
            } catch (\Exception $e) {
                throw new \Exception("Format tanggal tidak valid: {$row['tanggal_laporan']} (gunakan format dd/mm/yyyy)");
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
                    throw new \Exception("Format start_time tidak valid: {$row['start_time']} (gunakan format HH:mm)");
                }
            }

            if (!empty($row['end_time'])) {
                try {
                    $endTime = \Carbon\Carbon::createFromFormat('H:i', $row['end_time']);
                } catch (\Exception $e) {
                    throw new \Exception("Format end_time tidak valid: {$row['end_time']} (gunakan format HH:mm)");
                }
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
            'tanggal_laporan' => 'nullable|string',
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
}
