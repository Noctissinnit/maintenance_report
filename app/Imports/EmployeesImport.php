<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     * @return User|null
     */
    public function model(array $row)
    {
        // Skip baris kosong
        if (empty($row['name']) || empty($row['email'])) {
            return null;
        }

        // Cek apakah email sudah ada
        $existingUser = User::where('email', $row['email'])->first();
        if ($existingUser) {
            throw new \Exception("Email '{$row['email']}' sudah terdaftar");
        }

        $user = new User([
            'name' => trim($row['name']),
            'email' => trim($row['email']),
            'password' => Hash::make($row['password'] ?? 'password123'),
        ]);

        return $user;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:8',
        ];
    }
}
