<?php

namespace App\Imports;

use App\Models\Machine;
use App\Models\Line;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MachinesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     * @return Machine|null
     */
    public function model(array $row)
    {
        // Skip baris kosong
        if (empty($row['name']) || empty($row['line_name'])) {
            return null;
        }

        // Cek apakah line exists
        $line = Line::where('name', trim($row['line_name']))->first();
        if (!$line) {
            throw new \Exception("Line '{$row['line_name']}' tidak ditemukan");
        }

        // Cek apakah machine name sudah ada
        $existingMachine = Machine::where('name', $row['name'])->first();
        if ($existingMachine) {
            throw new \Exception("Nama mesin '{$row['name']}' sudah terdaftar");
        }

        // Cek code jika ada
        if (!empty($row['code'])) {
            $existingCode = Machine::where('code', $row['code'])->first();
            if ($existingCode) {
                throw new \Exception("Kode mesin '{$row['code']}' sudah terdaftar");
            }
        }

        $machine = new Machine([
            'name' => trim($row['name']),
            'code' => !empty($row['code']) ? trim($row['code']) : null,
            'line_id' => $line->id,
            'description' => !empty($row['description']) ? trim($row['description']) : null,
            'status' => strtolower($row['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
        ]);

        return $machine;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'line_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
