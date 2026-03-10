<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$role = \Spatie\Permission\Models\Role::findByName('department_head');
echo "Department Head Permissions: \n";
$perms = $role->getPermissionNames();
print_r($perms->toArray());

$user = \App\Models\User::where('email', 'departmenthead@maintenance.com')->first();
echo "\nUser Email: " . $user->email;
echo "\nUser Role: ";
print_r($user->getRoleNames()->toArray());
echo "\nUser Can view_own_laporan: " . ($user->can('view_own_laporan') ? 'YES' : 'NO');
echo "\nDone!\n";
