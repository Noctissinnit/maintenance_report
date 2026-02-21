<?php

namespace App\Imports;

use App\Models\Line;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LinesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     * @return Line|null
     */
    public function model(array $row)
    {
        // Skip baris kosong
        if (empty($row['name'])) {
            return null;
        }

        // Cek apakah name sudah ada
        $existingLine = Line::where('name', $row['name'])->first();
        if ($existingLine) {
            throw new \Exception("Nama line '{$row['name']}' sudah terdaftar");
        }

        // Cek code jika ada
        if (!empty($row['code'])) {
            $existingCode = Line::where('code', $row['code'])->first();
            if ($existingCode) {
                throw new \Exception("Kode line '{$row['code']}' sudah terdaftar");
            }
        }

        $line = new Line([
            'name' => trim($row['name']),
            'code' => !empty($row['code']) ? trim($row['code']) : null,
            'description' => !empty($row['description']) ? trim($row['description']) : null,
            'status' => strtolower($row['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
        ]);

        return $line;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
