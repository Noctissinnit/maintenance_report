<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat user admin
        $admin = User::create([
            'name' => 'Admin System',
            'email' => 'admin@maintenance.com',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        // Membuat user department head
        $departmentHead = User::create([
            'name' => 'Department Head',
            'email' => 'departmenthead@maintenance.com',
            'password' => Hash::make('password123'),
        ]);
        $departmentHead->assignRole('department_head');

        // Membuat user supervisor
        $supervisor = User::create([
            'name' => 'Supervisor',
            'email' => 'supervisor@maintenance.com',
            'password' => Hash::make('password123'),
        ]);
        $supervisor->assignRole('supervisor');

        // Membuat user operator 1
        $operator1 = User::create([
            'name' => 'Operator 1',
            'email' => 'operator1@maintenance.com',
            'password' => Hash::make('password123'),
        ]);
        $operator1->assignRole('operator');

        // Membuat user operator 2
        $operator2 = User::create([
            'name' => 'Operator 2',
            'email' => 'operator2@maintenance.com',
            'password' => Hash::make('password123'),
        ]);
        $operator2->assignRole('operator');

        // Membuat user operator 3
        $operator3 = User::create([
            'name' => 'Operator 3',
            'email' => 'operator3@maintenance.com',
            'password' => Hash::make('password123'),
        ]);
        $operator3->assignRole('operator');
    }
}
