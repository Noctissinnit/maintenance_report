<?php

namespace App\Http\Controllers;

use App\Models\LaporanHarian;
use App\Models\Machine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardPdfController extends Controller
{
    public function downloadDepartmentHeadPdf()
    {
        // Check permission
        if (!Auth::user()->can('view_department_dashboard')) {
            abort(403, 'Unauthorized');
        }

        // Get filter parameters (same as dashboard controller)
        $bulan = request('bulan') ?? now()->month;
        $tahun = request('tahun') ?? now()->year;
        $mesin = request('mesin');
        $line = request('line');
        $showAllTime = request('all_time') == '1';

        // Base query dengan filter
        $baseQuery = function() use ($tahun, $bulan, $mesin, $line, $showAllTime) {
            $q = LaporanHarian::query();
            
            if (!$showAllTime) {
                $q->whereYear('tanggal_laporan', $tahun)
                  ->whereMonth('tanggal_laporan', $bulan);
            }

            if ($mesin) {
                $q->where('mesin_name', $mesin);
            }

            if ($line) {
                $q->where('line', $line);
            }

            return $q;
        };

        // Query untuk metrics (dari DashboardController)
        $query = $baseQuery();
        
        // Total Laporan
        $totalLaporan = $baseQuery()->count();
        
        // Total Downtime
        $totalDowntimeFailed = $baseQuery()->where('jenis_pekerjaan', 'corrective')->where('downtime_min', '>', 0)->sum('downtime_min') ?? 0;
        $totalDowntime = $totalDowntimeFailed;
        
        // Average MTTR
        $avgMTTR = $baseQuery()->where('jenis_pekerjaan', 'corrective')->where('downtime_min', '>', 0)
            ->avg('downtime_min') ?? 0;
        
        // Get Daily Downtime data
        $allReports = $baseQuery()->get();
        
        $dailyDowntimes = $baseQuery()
            ->where('downtime_min', '>', 0)
            ->selectRaw('DATE(tanggal_laporan) as date, SUM(downtime_min) as total_downtime')
            ->groupBy(DB::raw('DATE(tanggal_laporan)'))
            ->get();
        
        // Machine Performance Metrics
        $activeMachinesQuery = Machine::where('status', 'active');
        if ($mesin) {
            $activeMachinesQuery->where('name', $mesin);
        }
        $activeMachinesCount = $activeMachinesQuery->count();
        $activeMachinesCount = max(1, $activeMachinesCount);
        
        $totalPlannedTime = 0;
        
        if ($showAllTime) {
            $earliestReport = $baseQuery()->orderBy('tanggal_laporan', 'asc')->first();
            $latestReport = $baseQuery()->orderBy('tanggal_laporan', 'desc')->first();
            
            if ($earliestReport && $latestReport) {
                $startCarbon = \Carbon\Carbon::parse($earliestReport->tanggal_laporan);
                $endCarbon = \Carbon\Carbon::parse($latestReport->tanggal_laporan);
                $totalDays = $endCarbon->diffInDays($startCarbon) + 1;
                $totalPlannedTime = $totalDays * 24 * 60 * $activeMachinesCount;
            }
        } else {
            $daysInMonth = \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;
            $totalPlannedTime = $daysInMonth * 24 * 60 * $activeMachinesCount;
        }
        
        // Total Breakdown
        $totalBreakdown = $baseQuery()->where('jenis_pekerjaan', 'corrective')->where('downtime_min', '>', 0)->count();
        
        $totalDowntimeMinutes = $totalDowntimeFailed;
        
        $totalPlannedTime = max(0, $totalPlannedTime);
        $totalDowntimeMinutes = max(0, $totalDowntimeMinutes);
        
        // Calculate percentages
        $downtimePercent = $totalPlannedTime > 0 ? ($totalDowntimeMinutes / $totalPlannedTime) * 100 : 0;
        $downtimePercent = min(100, $downtimePercent);
        $availability = 100 - $downtimePercent;
        
        // Maintenance Types
        $totalCorrectiveMaint = ($baseQuery()->where('jenis_pekerjaan', 'corrective')->sum('downtime_min') ?? 0) / 60;
        $totalPreventiveMaint = ($baseQuery()->where('jenis_pekerjaan', 'preventive')->sum('downtime_min') ?? 0) / 60;
        $totalChangeOver = ($baseQuery()->where('jenis_pekerjaan', 'change over product')->sum('downtime_min') ?? 0) / 60;
        
        // Top 10 Mesin dengan downtime terbanyak
        $topDowntimeMesin = $baseQuery()->select('mesin_name', DB::raw('SUM(downtime_min) as total_downtime'))
            ->groupBy('mesin_name')
            ->orderByDesc('total_downtime')
            ->limit(10)
            ->get();
        
        // Top 7 Breakdown by Line
        $topBreakdownLine = $baseQuery()->select('line', DB::raw('COUNT(*) as breakdown_count'))
            ->groupBy('line')
            ->orderByDesc('breakdown_count')
            ->limit(7)
            ->get();
        
        // Top 7 Breakdown by Catatan
        $topBreakdownCatatan = $baseQuery()->select('catatan', DB::raw('COUNT(*) as breakdown_count'))
            ->whereNotNull('catatan')
            ->where('catatan', '<>', '')
            ->groupBy('catatan')
            ->orderByDesc('breakdown_count')
            ->limit(7)
            ->get();
        
        // Monitoring Spare Part
        $spareParts = $baseQuery()->select('sparepart', DB::raw('SUM(qty_sparepart) as total_qty'))
            ->whereNotNull('sparepart')
            ->where('sparepart', '<>', '')
            ->groupBy('sparepart')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();
        
        // Machine Performance
        $machinePerformance = $baseQuery()->select('mesin_name', DB::raw('COUNT(*) as count'))
            ->groupBy('mesin_name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Get unique values
        $allMesins = LaporanHarian::distinct()->pluck('mesin_name')->sort();
        $allLines = LaporanHarian::distinct()->pluck('line')->sort();

        // MTBF Metrics
        $machines = Machine::where('status', 'active')->with('line')->get();
        $mtbfData = [];
        $totalMTBFHours = 0;
        $mtbfMachineCount = 0;

        foreach ($machines as $machine) {
            if ($showAllTime) {
                $mtbf = $machine->calculateMTBFAllTime();
            } else {
                $mtbf = $machine->calculateMTBF($tahun, $bulan);
            }
            if ($mtbf['failure_count'] > 0) {
                $mtbfData[] = $mtbf;
                $totalMTBFHours += $mtbf['mtbf_hours'];
                $mtbfMachineCount++;
            }
        }

        usort($mtbfData, function ($a, $b) {
            return $b['mtbf_hours'] <=> $a['mtbf_hours'];
        });

        $avgMTBFHours = $mtbfMachineCount > 0 ? $totalMTBFHours / $mtbfMachineCount : 0;
        $topReliableMachines = array_slice($mtbfData, 0, 5);
        $worstMachines = array_slice(array_reverse($mtbfData), 0, 5);

        // Prepare data for PDF
        $data = compact(
            'totalLaporan',
            'totalDowntime',
            'totalDowntimeMinutes',
            'avgMTTR',
            'availability',
            'downtimePercent',
            'topDowntimeMesin',
            'topBreakdownLine',
            'topBreakdownCatatan',
            'spareParts',
            'machinePerformance',
            'totalPlannedTime',
            'totalBreakdown',
            'totalCorrectiveMaint',
            'totalPreventiveMaint',
            'totalChangeOver',
            'bulan',
            'tahun',
            'mesin',
            'line',
            'topReliableMachines',
            'worstMachines',
            'avgMTBFHours'
        );

        // Generate PDF
        $pdf = Pdf::loadView('pdf.department-head-dashboard', $data)
            ->setOption(['defaultFont' => 'sans-serif', 'isHtml5ParserEnabled' => true]);
        
        // Download filename
        $filename = 'Dashboard-' . \Carbon\Carbon::createFromFormat('n', $bulan)->format('F') . '-' . $tahun . '.pdf';
        
        return $pdf->download($filename);
    }
}
