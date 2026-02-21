<?php

namespace App\Imports;

use App\Models\SparePart;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SparePartsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     * @return SparePart|null
     */
    public function model(array $row)
    {
        // Skip baris kosong
        if (empty($row['name'])) {
            return null;
        }

        // Cek apakah name sudah ada
        $existingSparePart = SparePart::where('name', $row['name'])->first();
        if ($existingSparePart) {
            throw new \Exception("Nama spare part '{$row['name']}' sudah terdaftar");
        }

        // Cek code jika ada
        if (!empty($row['code'])) {
            $existingCode = SparePart::where('code', $row['code'])->first();
            if ($existingCode) {
                throw new \Exception("Kode spare part '{$row['code']}' sudah terdaftar");
            }
        }

        $sparePart = new SparePart([
            'name' => trim($row['name']),
            'code' => !empty($row['code']) ? trim($row['code']) : null,
            'description' => !empty($row['description']) ? trim($row['description']) : null,
            'category' => !empty($row['category']) ? trim($row['category']) : null,
            'status' => strtolower($row['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
        ]);

        return $sparePart;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
