<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Cek data di semua bulan 2026 ===\n";
for ($m = 1; $m <= 12; $m++) {
  $count = \App\Models\LaporanHarian::whereYear('tanggal_laporan', 2026)
    ->whereMonth('tanggal_laporan', $m)
    ->count();
  $downtime = \App\Models\LaporanHarian::whereYear('tanggal_laporan', 2026)
    ->whereMonth('tanggal_laporan', $m)
    ->where('downtime_min', '>', 0)
    ->sum('downtime_min');
  echo "Bulan $m: " . $count . " reports, Downtime: " . $downtime . " min\n";
}

echo "\n=== Total data keseluruhan ===\n";
$total = \App\Models\LaporanHarian::count();
echo "Total laporan: " . $total . "\n";

// Cek tanggal terbaru
$latest = \App\Models\LaporanHarian::max('tanggal_laporan');
echo "Latest laporan date: " . $latest . "\n";

// Cek minimal tanggal
$earliest = \App\Models\LaporanHarian::min('tanggal_laporan');
echo "Earliest laporan date: " . $earliest . "\n";
