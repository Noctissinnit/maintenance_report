<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$bulan = 3;  // Maret
$tahun = 2026;

$daysInMonth = \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;
$totalPlannedTime = $daysInMonth * 24 * 60;

echo "=== Maret 2026 ===\n";
echo "Days in month: " . $daysInMonth . "\n";
echo "Total Planned Time (menit): " . $totalPlannedTime . "\n";
echo "Total Planned Time (jam): " . ($totalPlannedTime / 60) . "\n";

$dailyDowntimes = \App\Models\LaporanHarian::whereYear('tanggal_laporan', $tahun)
  ->whereMonth('tanggal_laporan', $bulan)
  ->where('downtime_min', '>', 0)
  ->selectRaw('DATE(tanggal_laporan) as date, SUM(downtime_min) as total_downtime')
  ->groupBy(\Illuminate\Support\Facades\DB::raw('DATE(tanggal_laporan)'))
  ->get();

echo "\nDaily downtime entries: " . count($dailyDowntimes) . "\n";

$cappedTotalDowntime = 0;
foreach ($dailyDowntimes as $day) {
  $cap = min($day->total_downtime, 480);
  echo "Date: " . $day->date . ", Downtime: " . $day->total_downtime . ", Capped: " . $cap . "\n";
  $cappedTotalDowntime += $cap;
}

echo "\nTotal Downtime (menit): " . $cappedTotalDowntime . "\n";
echo "Total Downtime (jam): " . ($cappedTotalDowntime / 60) . "\n";

$minDowntime = min($cappedTotalDowntime, $totalPlannedTime);
echo "Min(Downtime, PlannedTime): " . $minDowntime . " menit\n";

$operationTime = ($totalPlannedTime - $minDowntime) / 60;
echo "Operation Time (jam): " . number_format($operationTime, 2) . "\n";
