<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanHarianController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SparePartController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\TemplateExportController;
use App\Http\Controllers\MTBFController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Template Export Routes
    Route::get('/templates/download-machine', [TemplateExportController::class, 'downloadMachineTemplate'])->name('templates.download-machine');
    Route::get('/templates/download-line', [TemplateExportController::class, 'downloadLineTemplate'])->name('templates.download-line');
    Route::get('/templates/download-spare-part', [TemplateExportController::class, 'downloadSparePartTemplate'])->name('templates.download-spare-part');
    
    // Laporan Routes
    Route::get('/laporan', [LaporanHarianController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/create', [LaporanHarianController::class, 'create'])->name('laporan.create');
    Route::post('/laporan', [LaporanHarianController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{id}/edit', [LaporanHarianController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{id}', [LaporanHarianController::class, 'update'])->name('laporan.update');
    Route::delete('/laporan/{id}', [LaporanHarianController::class, 'destroy'])->name('laporan.destroy');
    
    // Employee Management (Admin only)
    Route::middleware(['can:manage_employees'])->group(function () {
        Route::resource('employees', EmployeeController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });

    // Line Management (Admin only)
    Route::middleware(['can:manage_machines'])->group(function () {
        Route::get('/lines', [LineController::class, 'index'])->name('lines.index');
        Route::get('/lines/create', [LineController::class, 'create'])->name('lines.create');
        Route::post('/lines', [LineController::class, 'store'])->name('lines.store');
        Route::get('/lines/{line}/edit', [LineController::class, 'edit'])->name('lines.edit');
        Route::put('/lines/{line}', [LineController::class, 'update'])->name('lines.update');
        Route::delete('/lines/{line}', [LineController::class, 'destroy'])->name('lines.destroy');
    });

    // Machine Management (Admin only)
    Route::middleware(['can:manage_machines'])->group(function () {
        Route::get('/machines', [MachineController::class, 'index'])->name('machines.index');
        Route::get('/machines/create', [MachineController::class, 'create'])->name('machines.create');
        Route::post('/machines', [MachineController::class, 'store'])->name('machines.store');
        Route::get('/machines/{machine}/edit', [MachineController::class, 'edit'])->name('machines.edit');
        Route::put('/machines/{machine}', [MachineController::class, 'update'])->name('machines.update');
        Route::delete('/machines/{machine}', [MachineController::class, 'destroy'])->name('machines.destroy');
        Route::get('/machines/export', [MachineController::class, 'export'])->name('machines.export');
        Route::get('/machines/import-form', [MachineController::class, 'importForm'])->name('machines.import-form');
        Route::post('/machines/import', [MachineController::class, 'import'])->name('machines.import');
    });

    // Spare Part Management (Admin only)
    Route::middleware(['can:manage_spare_parts'])->group(function () {
        Route::get('/spare-parts', [SparePartController::class, 'index'])->name('spare-parts.index');
        Route::get('/spare-parts/create', [SparePartController::class, 'create'])->name('spare-parts.create');
        Route::post('/spare-parts', [SparePartController::class, 'store'])->name('spare-parts.store');
        Route::get('/spare-parts/{sparePart}/edit', [SparePartController::class, 'edit'])->name('spare-parts.edit');
        Route::put('/spare-parts/{sparePart}', [SparePartController::class, 'update'])->name('spare-parts.update');
        Route::delete('/spare-parts/{sparePart}', [SparePartController::class, 'destroy'])->name('spare-parts.destroy');
        Route::get('/spare-parts/export', [SparePartController::class, 'export'])->name('spare-parts.export');
        Route::get('/spare-parts/import-form', [SparePartController::class, 'importForm'])->name('spare-parts.import-form');
        Route::post('/spare-parts/import', [SparePartController::class, 'import'])->name('spare-parts.import');
        Route::get('/spare-parts/monitoring', [SparePartController::class, 'monitoring'])->name('spare-parts.monitoring');
    });
    
    // MTBF Analysis Routes
    Route::get('/mtbf', [MTBFController::class, 'index'])->name('mtbf.index');
    Route::get('/machines/{machine}/mtbf', [MTBFController::class, 'show'])->name('mtbf.show');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
