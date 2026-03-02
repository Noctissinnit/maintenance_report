<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$now = \Carbon\Carbon::now();
echo "Current month: " . $now->month . ", Current year: " . $now->year . "\n";

$count = \App\Models\LaporanHarian::whereYear('tanggal_laporan', $now->year)
  ->whereMonth('tanggal_laporan', $now->month)
  ->count();
echo "Total laporan untuk bulan ini: " . $count . "\n";

$daysInMonth = \Carbon\Carbon::create($now->year, $now->month)->daysInMonth;
echo "Jumlah hari dalam bulan ini: " . $daysInMonth . "\n";
$totalPlannedTime = $daysInMonth * 8 * 60;
echo "Total Planned Time (menit): " . $totalPlannedTime . "\n";

$totalDowntime = \App\Models\LaporanHarian::whereYear('tanggal_laporan', $now->year)
  ->whereMonth('tanggal_laporan', $now->month)
  ->where('downtime_min', '>', 0)
  ->sum('downtime_min');
echo "Total Downtime (menit): " . $totalDowntime . "\n";

$operationTime = ($totalPlannedTime - $totalDowntime) / 60;
echo "Operation Time (jam): " . $operationTime . "\n";

// Cek beberapa laporan
echo "\n=== Sample Data ===\n";
$samples = \App\Models\LaporanHarian::whereYear('tanggal_laporan', $now->year)
  ->whereMonth('tanggal_laporan', $now->month)
  ->limit(5)
  ->get();

foreach ($samples as $s) {
  echo "ID: " . $s->id . ", Date: " . $s->tanggal_laporan . ", Downtime: " . $s->downtime_min . " min\n";
}
