<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$bulan = 1;  // Januari
$tahun = 2026;

$daysInMonth = \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;
$totalPlannedTime = $daysInMonth * 24 * 60;

echo "=== Januari 2026 ===\n";
echo "Days in month: " . $daysInMonth . "\n";
echo "Total Planned Time (menit): " . $totalPlannedTime . "\n";
echo "Total Planned Time (jam): " . ($totalPlannedTime / 60) . "\n\n";

$totalDowntime = \App\Models\LaporanHarian::whereYear('tanggal_laporan', $tahun)
  ->whereMonth('tanggal_laporan', $bulan)
  ->where('downtime_min', '>', 0)
  ->sum('downtime_min');
echo "Total Downtime (uncapped) (menit): " . $totalDowntime . "\n";

$dailyDowntimes = \App\Models\LaporanHarian::whereYear('tanggal_laporan', $tahun)
  ->whereMonth('tanggal_laporan', $bulan)
  ->where('downtime_min', '>', 0)
  ->selectRaw('DATE(tanggal_laporan) as date, SUM(downtime_min) as total_downtime')
  ->groupBy(\Illuminate\Support\Facades\DB::raw('DATE(tanggal_laporan)'))
  ->get();

echo "Daily downtime entries: " . count($dailyDowntimes) . "\n";

$cappedTotalDowntime = 0;
$daysWithExcess = 0;
foreach ($dailyDowntimes as $day) {
  $cap = min($day->total_downtime, 480);
  if ($cap < $day->total_downtime) {
    $daysWithExcess++;
  }
  $cappedTotalDowntime += $cap;
}

echo "Days that exceeded 8 hours: " . $daysWithExcess . "\n";
echo "Total Downtime (capped) (menit): " . $cappedTotalDowntime . "\n";
echo "Total Downtime (capped) (jam): " . ($cappedTotalDowntime / 60) . "\n\n";

// Calculate metrics
$downtimePercent = $totalPlannedTime > 0 ? ($cappedTotalDowntime / $totalPlannedTime) * 100 : 0;
$availability = 100 - $downtimePercent;
$operationTime = ($totalPlannedTime - $cappedTotalDowntime) / 60;

echo "=== METRICS ===\n";
echo "Planned Time: " . number_format($totalPlannedTime / 60, 2) . " jam\n";
echo "Down Time: " . number_format($cappedTotalDowntime / 60, 2) . " jam (capped)\n";
echo "Operation Time: " . number_format($operationTime, 2) . " jam\n";
echo "Availability: " . number_format($availability, 2) . "%\n";
echo "Downtime %: " . number_format($downtimePercent, 2) . "%\n";
