<?php

namespace App\Http\Controllers;

use App\Models\LaporanHarian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Route ke dashboard berdasarkan role
        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('department_head')) {
            return $this->departmentHeadDashboard();
        } elseif ($user->hasRole('supervisor')) {
            return $this->supervisorDashboard();
        } elseif ($user->hasRole('operator')) {
            return $this->operatorDashboard();
        }

        abort(403, 'Unauthorized access');
    }

    private function adminDashboard()
    {
        // Statistik Sistem
        $totalUsers = \App\Models\User::count();
        $totalLaporan = LaporanHarian::count();
        $totalDowntime = LaporanHarian::sum('downtime_min') ?? 0;
        
        // Users by role
        $adminCount = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'admin');
        })->count();
        
        $departmentHeadCount = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'department_head');
        })->count();
        
        $supervisorCount = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'supervisor');
        })->count();

        $operatorCount = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'operator');
        })->count();

        // Latest reports
        $latestLaporan = LaporanHarian::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Laporan per user
        $laporanPerUser = LaporanHarian::select('user_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return view('dashboard.admin', compact(
            'totalUsers',
            'totalLaporan',
            'totalDowntime',
            'adminCount',
            'departmentHeadCount',
            'supervisorCount',
            'operatorCount',
            'latestLaporan',
            'laporanPerUser'
        ));
    }

    private function departmentHeadDashboard()
    {
        // Check if user has permission
        if (!Auth::user()->can('view_department_dashboard')) {
            abort(403, 'Unauthorized');
        }

        // Get filter parameters
        $bulan = request('bulan') ?? now()->month;
        $tahun = request('tahun') ?? now()->year;
        $mesin = request('mesin');
        $line = request('line');

        // Base query dengan filter
        $baseQuery = function() use ($tahun, $bulan, $mesin, $line) {
            $q = LaporanHarian::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan);

            if ($mesin) {
                $q->where('mesin_name', $mesin);
            }

            if ($line) {
                $q->where('line', $line);
            }

            return $q;
        };

        // Query untuk metrics
        $query = $baseQuery();
        
        // Total Laporan
        $totalLaporan = $baseQuery()->count();
        
        // Total Downtime (menit)
        $totalDowntime = $baseQuery()->sum('downtime_min') ?? 0;
        
        // Average MTTR (Mean Time To Repair)
        $avgMTTR = $baseQuery()->where('downtime_min', '>', 0)
            ->avg('downtime_min') ?? 0;
        
        // Average MTBF (Mean Time Between Failures)
        $avgMTBF = $baseQuery()->where('between_failure_min', '>', 0)
            ->avg('between_failure_min') ?? 0;
        
        // Hitung Availability dan Downtime Percentage
        $maxDowntime = max(14400, $totalDowntime);
        $downtimePercent = $maxDowntime > 0 ? ($totalDowntime / $maxDowntime) * 100 : 0;
        $availability = 100 - $downtimePercent;
        
        // Machine Performance Metrics
        $totalPlannedTime = ($baseQuery()->count() * 8); // Assuming 8 jam per shift
        $totalBreakdown = $baseQuery()->where('downtime_min', '>', 0)->count();
        
        // Maintenance Types (Convert menit to jam)
        // Corrective = downtime_min (perbaikan dari kerusakan)
        $totalCorrectiveMaint = ($baseQuery()->sum('downtime_min') ?? 0) / 60;
        
        // Preventive dapat diestimasi dari laporan maintenance yang direncanakan
        $totalPreventiveMaint = 0; // Default 0 jika tidak ada data
        
        // Predictive dapat diestimasi dari data tertentu
        $totalPredictive = 0; // Default 0 jika tidak ada data
        
        // Change Over = waktu persiapan produk baru (bisa dihitung dari selisih waktu)
        $totalChangeOver = 0; // Default 0 jika tidak ada data
        
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
        
        // Top 7 Breakdown by Catatan (Jenis Kerusakan)
        $topBreakdownCatatan = $baseQuery()->select('catatan', DB::raw('COUNT(*) as breakdown_count'))
            ->whereNotNull('catatan')
            ->where('catatan', '<>', '')
            ->groupBy('catatan')
            ->orderByDesc('breakdown_count')
            ->limit(7)
            ->get();
        
        // Monitoring Spare Part (per bulan)
        $spareParts = $baseQuery()->select('sparepart', DB::raw('SUM(qty_sparepart) as total_qty'))
            ->whereNotNull('sparepart')
            ->where('sparepart', '<>', '')
            ->groupBy('sparepart')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();
        
        // Machine Performance by Type
        $machinePerformance = $baseQuery()->select('mesin_name', DB::raw('COUNT(*) as count'))
            ->groupBy('mesin_name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Get unique values untuk filter
        $allMesins = LaporanHarian::distinct()->pluck('mesin_name')->sort();
        $allLines = LaporanHarian::distinct()->pluck('line')->sort();

        return view('dashboard.department-head', compact(
            'totalLaporan',
            'totalDowntime',
            'avgMTTR',
            'avgMTBF',
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
            'totalPredictive',
            'totalChangeOver',
            'bulan',
            'tahun',
            'mesin',
            'line',
            'allMesins',
            'allLines'
        ));
    }

    private function supervisorDashboard()
    {
        // Check if user has permission
        if (!Auth::user()->can('view_dashboard')) {
            abort(403, 'Unauthorized');
        }

        // Get filter parameters
        $bulan = request('bulan') ?? now()->month;
        $tahun = request('tahun') ?? now()->year;
        $mesin = request('mesin');
        $line = request('line');

        // Base query dengan filter
        $baseQuery = function() use ($tahun, $bulan, $mesin, $line) {
            $q = LaporanHarian::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan);

            if ($mesin) {
                $q->where('mesin_name', $mesin);
            }

            if ($line) {
                $q->where('line', $line);
            }

            return $q;
        };

        // Query untuk metrics
        $query = $baseQuery();
        
        // Total Laporan
        $totalLaporan = $baseQuery()->count();
        
        // Total Downtime (menit)
        $totalDowntime = $baseQuery()->sum('downtime_min') ?? 0;
        
        // Average MTTR (Mean Time To Repair)
        $avgMTTR = $baseQuery()->where('downtime_min', '>', 0)
            ->avg('downtime_min') ?? 0;
        
        // Average MTBF (Mean Time Between Failures)
        $avgMTBF = $baseQuery()->where('between_failure_min', '>', 0)
            ->avg('between_failure_min') ?? 0;
        
        // Hitung Availability dan Downtime Percentage
        $maxDowntime = max(14400, $totalDowntime);
        $downtimePercent = $maxDowntime > 0 ? ($totalDowntime / $maxDowntime) * 100 : 0;
        $availability = 100 - $downtimePercent;
        
        // Machine Performance Metrics
        $totalPlannedTime = ($baseQuery()->count() * 8); // Assuming 8 jam per shift
        $totalBreakdown = $baseQuery()->where('downtime_min', '>', 0)->count();
        
        // Maintenance Types (Convert menit to jam)
        // Corrective = downtime_min (perbaikan dari kerusakan)
        $totalCorrectiveMaint = ($baseQuery()->sum('downtime_min') ?? 0) / 60;
        
        // Preventive dapat diestimasi dari laporan maintenance yang direncanakan
        $totalPreventiveMaint = 0; // Default 0 jika tidak ada data
        
        // Predictive dapat diestimasi dari data tertentu
        $totalPredictive = 0; // Default 0 jika tidak ada data
        
        // Change Over = waktu persiapan produk baru (bisa dihitung dari selisih waktu)
        $totalChangeOver = 0; // Default 0 jika tidak ada data
        
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
        
        // Top 7 Breakdown by Catatan (Jenis Kerusakan)
        $topBreakdownCatatan = $baseQuery()->select('catatan', DB::raw('COUNT(*) as breakdown_count'))
            ->whereNotNull('catatan')
            ->where('catatan', '<>', '')
            ->groupBy('catatan')
            ->orderByDesc('breakdown_count')
            ->limit(7)
            ->get();
        
        // Monitoring Spare Part (per bulan)
        $spareParts = $baseQuery()->select('sparepart', DB::raw('SUM(qty_sparepart) as total_qty'))
            ->whereNotNull('sparepart')
            ->where('sparepart', '<>', '')
            ->groupBy('sparepart')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();
        
        // Machine Performance by Type
        $machinePerformance = $baseQuery()->select('mesin_name', DB::raw('COUNT(*) as count'))
            ->groupBy('mesin_name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Get unique values untuk filter
        $allMesins = LaporanHarian::distinct()->pluck('mesin_name')->sort();
        $allLines = LaporanHarian::distinct()->pluck('line')->sort();

        return view('dashboard.supervisor', compact(
            'totalLaporan',
            'totalDowntime',
            'avgMTTR',
            'avgMTBF',
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
            'totalPredictive',
            'totalChangeOver',
            'bulan',
            'tahun',
            'mesin',
            'line',
            'allMesins',
            'allLines'
        ));
    }

    private function operatorDashboard()
    {
        // Check if user has permission
        if (!Auth::user()->can('view_own_laporan')) {
            abort(403, 'Unauthorized');
        }

        $user = Auth::user();
        
        // Laporan user
        $totalLaporan = LaporanHarian::where('user_id', $user->id)->count();
        $totalDowntime = LaporanHarian::where('user_id', $user->id)->sum('downtime_min') ?? 0;
        $totalLaporanHarian = LaporanHarian::where('user_id', $user->id)
            ->where('tipe_laporan', 'harian')->count();
        $totalLaporanMingguan = LaporanHarian::where('user_id', $user->id)
            ->where('tipe_laporan', 'mingguan')->count();
        
        // Latest laporan
        $latestLaporan = LaporanHarian::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Laporan per mesin
        $laporanPerMesin = LaporanHarian::where('user_id', $user->id)
            ->select('mesin_name', DB::raw('COUNT(*) as count'), DB::raw('SUM(downtime_min) as total_downtime'))
            ->groupBy('mesin_name')
            ->get();

        // Laporan per tipe
        $laporanPerTipe = LaporanHarian::where('user_id', $user->id)
            ->select('tipe_laporan', DB::raw('COUNT(*) as count'))
            ->groupBy('tipe_laporan')
            ->get();

        // Statistik downtime
        $avgDowntime = LaporanHarian::where('user_id', $user->id)
            ->where('downtime_min', '>', 0)
            ->avg('downtime_min') ?? 0;

        return view('dashboard.operator', compact(
            'totalLaporan',
            'totalDowntime',
            'totalLaporanHarian',
            'totalLaporanMingguan',
            'latestLaporan',
            'laporanPerMesin',
            'laporanPerTipe',
            'avgDowntime'
        ));
    }
}
