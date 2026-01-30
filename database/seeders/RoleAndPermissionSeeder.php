<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create permissions
        $permissions = [
            // Dashboard permissions
            'view_dashboard',
            'view_all_laporan',
            'view_department_dashboard',
            
            // Laporan permissions
            'create_laporan',
            'edit_laporan',
            'delete_laporan',
            'view_own_laporan',
            
            // Employee management permissions
            'manage_employees',
            
            // Machine management permissions
            'manage_machines',
            
            // Spare part management permissions
            'manage_spare_parts',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $operatorRole = Role::firstOrCreate(['name' => 'operator', 'guard_name' => 'web']);
        $supervisorRole = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $departmentHeadRole = Role::firstOrCreate(['name' => 'department_head', 'guard_name' => 'web']);

        // Reset cache before assigning permissions
        app()['cache']->forget('spatie.permission.cache');

        // Assign permissions to admin (semua permissions dari supervisor + management)
        $adminRole->syncPermissions([
            'view_dashboard',
            'view_all_laporan',
            'manage_employees',
            'manage_machines',
            'manage_spare_parts',
            'view_department_dashboard',
            'view_own_laporan',
            'create_laporan',
            'edit_laporan',
            'delete_laporan',
        ]);

        // Assign permissions to operator (pengganti dari karyawan)
        $operatorRole->syncPermissions([
            'create_laporan',
            'edit_laporan',
            'delete_laporan',
            'view_own_laporan',
        ]);

        // Assign permissions to supervisor (buat laporan + lihat dashboard department head)
        $supervisorRole->syncPermissions([
            'create_laporan',
            'edit_laporan',
            'delete_laporan',
            'view_own_laporan',
            'view_dashboard',
            'view_all_laporan',
        ]);

        // Assign permissions to department_head (monitor semua dashboard)
        $departmentHeadRole->syncPermissions([
            'view_dashboard',
            'view_all_laporan',
            'view_department_dashboard',
        ]);

        // Final cache reset
        app()['cache']->forget('spatie.permission.cache');
    }
}
