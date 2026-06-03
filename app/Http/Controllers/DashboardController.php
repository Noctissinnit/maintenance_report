<?php

namespace App\Http\Controllers;

use App\Models\LaporanHarian;
use App\Models\Machine;
use App\Models\Line;
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

    /**
     * Get downtime by scope (Electrical, Mechanical, Utility, Building) in hours
     */
    private function getDowntimeByScope($tahun, $bulan, $baseQueryCallback = null)
    {
        if ($baseQueryCallback === null) {
            // Default query for given month/year
            $baseQueryCallback = function() use ($tahun, $bulan) {
                return LaporanHarian::whereYear('tanggal_laporan', $tahun)
                    ->whereMonth('tanggal_laporan', $bulan);
            };
        }

        $scopes = ['Electrik', 'Mekanik', 'Utility', 'Building'];
        $downtimeByScope = [];

        foreach ($scopes as $scope) {
            $query = $baseQueryCallback();
            $downtimeMinutes = $query->where('scope', $scope)
                ->whereIn('jenis_pekerjaan', ['corrective', 'preventive', 'change over product'])
                ->sum('downtime_min') ?? 0;
            
            // Convert minutes to hours
            $downtimeHours = round($downtimeMinutes / 60, 2);
            
            $downtimeByScope[] = [
                'scope' => $scope,
                'downtime_hours' => $downtimeHours,
                'downtime_minutes' => $downtimeMinutes
            ];
        }

        return $downtimeByScope;
    }

    private function departmentHeadDashboard()
    {
        // Check if user has permission
        if (!Auth::user()->can('view_department_dashboard')) {
            abort(403, 'Unauthorized');
        }

        // Get filter parameters
        $filterMode = request('filter_mode') ?? 'month';
        $bulan = request('bulan') ?? now()->month;
        $tahun = request('tahun') ?? now()->year;
        $dariTanggal = request('dari_tanggal') ?? date('Y-m-01');
        $sampaiTanggal = request('sampai_tanggal') ?? date('Y-m-d');
        $mesin = request('mesin');
        $line = request('line');
        $showAllTime = request('all_time') == '1';

        // Base query dengan filter
        $baseQuery = function() use ($tahun, $bulan, $mesin, $line, $showAllTime, $filterMode, $dariTanggal, $sampaiTanggal) {
            $q = LaporanHarian::query();
            
            // Only apply date filters if not showing all time data
            if (!$showAllTime) {
                if ($filterMode === 'date_range') {
                    // Use date range filter
                    $q->whereBetween('tanggal_laporan', [$dariTanggal, $sampaiTanggal]);
                } else {
                    // Use month/year filter (default)
                    $q->whereYear('tanggal_laporan', $tahun)
                      ->whereMonth('tanggal_laporan', $bulan);
                }
            }

            if ($mesin) {
                $q->where('mesin_name', $mesin);
            }

            if ($line) {
                $q->where('line', $line);
            }

            // Filter by line_status = 'on' (exclude 'off' lines that don't count downtime)
            // Include null values for backward compatibility with old records
            $q->where(function($subQuery) {
                $subQuery->where('line_status', 'on')
                         ->orWhereNull('line_status');
            });

            return $q;
        };

        // Query untuk metrics
        $query = $baseQuery();
        
        // Total Laporan
        $totalLaporan = $baseQuery()->count();
        
        // Total Downtime (menit) = Corrective + Preventive Maintenance downtime
        // Revision: Downtime includes both corrective and preventive maintenance
        $totalDowntimeCorrective = $baseQuery()->where('jenis_pekerjaan', 'corrective')->where('downtime_min', '>', 0)->sum('downtime_min') ?? 0;
        $totalDowntimePreventive = $baseQuery()->where('jenis_pekerjaan', 'preventive')->where('downtime_min', '>', 0)->sum('downtime_min') ?? 0;
        $totalDowntime = $totalDowntimeCorrective + $totalDowntimePreventive;
        
        // Average MTTR (Mean Time To Repair) - rata-rata dari laporan corrective yang punya downtime
        $avgMTTR = $baseQuery()->where('jenis_pekerjaan', 'corrective')->where('downtime_min', '>', 0)
            ->avg('downtime_min') ?? 0;
        
        // Average MTBF will be calculated from Machine model below
        $avgMTBF = 0;
        
        // Get Daily Downtime data first (needed for both Planned Time calc and downtime summation)
        $allReports = $baseQuery()->get();
        
        $dailyDowntimes = $baseQuery()
            ->where('downtime_min', '>', 0)
            ->selectRaw('DATE(tanggal_laporan) as date, SUM(downtime_min) as total_downtime')
            ->groupBy(DB::raw('DATE(tanggal_laporan)'))
            ->get();
        
        
        $totalPlannedTime = 0;
        
       
        $plannedTimeFromReports = $baseQuery()->sum('planned_time_minutes') ?? 0;
        
        if ($plannedTimeFromReports > 0) {
            
            $totalPlannedTime = $plannedTimeFromReports;
        } else if ($showAllTime) {
   
            $plannedTimes = \App\Models\PlannedTime::all()->sum('planned_time_minutes');
            if ($plannedTimes > 0) {
                $totalPlannedTime = $plannedTimes;
            } else {
                // Fallback: calculate from earliest and latest report dates
                $earliestReport = $baseQuery()->orderBy('tanggal_laporan', 'asc')->first();
                $latestReport = $baseQuery()->orderBy('tanggal_laporan', 'desc')->first();
                
                if ($earliestReport && $latestReport) {
                    $startCarbon = \Carbon\Carbon::parse($earliestReport->tanggal_laporan);
                    $endCarbon = \Carbon\Carbon::parse($latestReport->tanggal_laporan);
                    $totalDays = $endCarbon->diffInDays($startCarbon) + 1;
                    
                    // Get count of active machines
                    $activeMachinesQuery = Machine::where('status', 'active');
                    if ($mesin) {
                        $activeMachinesQuery->where('name', $mesin);
                    }
                    $activeMachinesCount = max(1, $activeMachinesQuery->count());
                    
                    // Formula: days × 24 hours × 60 minutes × number of active machines
                    $totalPlannedTime = $totalDays * 24 * 60 * $activeMachinesCount;
                }
            }
        } else {
            // For date range or specific month, try global PlannedTime table first
            if ($filterMode === 'date_range') {
                // For date range, fallback to calculation
                $startCarbon = \Carbon\Carbon::parse($dariTanggal);
                $endCarbon = \Carbon\Carbon::parse($sampaiTanggal);
                $totalDays = $endCarbon->diffInDays($startCarbon) + 1;
                
                // Get count of active machines
                $activeMachinesQuery = Machine::where('status', 'active');
                if ($mesin) {
                    $activeMachinesQuery->where('name', $mesin);
                }
                $activeMachinesCount = max(1, $activeMachinesQuery->count());
                
                // Formula: days × 24 hours × 60 minutes × number of active machines
                $totalPlannedTime = $totalDays * 24 * 60 * $activeMachinesCount;
            } else {
                // For specific month, check global PlannedTime database
                $plannedTimeRecord = \App\Models\PlannedTime::where('year', $tahun)
                    ->where('month', $bulan)
                    ->first();
                
                if ($plannedTimeRecord) {
                    // Use manually entered planned time from PPIC
                    $totalPlannedTime = $plannedTimeRecord->planned_time_minutes;
                } else {
                    // Fallback: calculate from days in month and active machines
                    $daysInMonth = \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;
                    
                    // Get count of active machines
                    $activeMachinesQuery = Machine::where('status', 'active');
                    if ($mesin) {
                        $activeMachinesQuery->where('name', $mesin);
                    }
                    $activeMachinesCount = max(1, $activeMachinesQuery->count());
                    
                    // Formula: days × 24 hours × 60 minutes × number of active machines
                    $totalPlannedTime = $daysInMonth * 24 * 60 * $activeMachinesCount;
                }
            }
        }
        
        // Total Breakdown = jumlah laporan corrective dengan downtime
        // (Breakdown = failure events, separate from downtime hours)
        $totalBreakdown = $baseQuery()->where('jenis_pekerjaan', 'corrective')->where('downtime_min', '>', 0)->count();
        
        // Total Downtime Minutes = Corrective + Preventive (calculated above)
        $totalDowntimeMinutes = $totalDowntime;
        
        // Ensure values are positive and valid
        $totalPlannedTime = max(0, $totalPlannedTime);
        $totalDowntimeMinutes = max(0, $totalDowntimeMinutes);
        
        // Hitung Availability dan Downtime Percentage dengan benar
        $downtimePercent = $totalPlannedTime > 0 ? ($totalDowntimeMinutes / $totalPlannedTime) * 100 : 0;
        $downtimePercent = min(100, $downtimePercent); // Cap at 100%
        $availability = 100 - $downtimePercent;
        
        // Maintenance Types (Convert menit to jam)
        // Corrective = downtime_min dari laporan jenis_pekerjaan = 'corrective'
        $totalCorrectiveMaint = ($baseQuery()->where('jenis_pekerjaan', 'corrective')->sum('downtime_min') ?? 0) / 60;
        
        // Preventive = downtime_min dari laporan jenis_pekerjaan = 'preventive'
        $totalPreventiveMaint = ($baseQuery()->where('jenis_pekerjaan', 'preventive')->sum('downtime_min') ?? 0) / 60;
        
        // Change Over Product = downtime_min dari laporan jenis_pekerjaan = 'change over product'
        $totalChangeOver = ($baseQuery()->where('jenis_pekerjaan', 'change over product')->sum('downtime_min') ?? 0) / 60;
        
        // Top 10 Mesin dengan downtime terbanyak
        $topDowntimeMesin = $baseQuery()->select('mesin_name', DB::raw('SUM(downtime_min) as total_downtime'))
            ->groupBy('mesin_name')
            ->orderByDesc('total_downtime')
            ->limit(10)
            ->get();
        
        // All Breakdown by Line (showing all lines including those with 0 breakdown count)
        $allLines = Line::where('status', 'on')->get();
        $breakdownByLine = $baseQuery()->where('line_status', 'on')
            ->select('line', 
                DB::raw('COUNT(*) as breakdown_count'),
                DB::raw('SUM(downtime_min) as total_downtime_min'))
            ->groupBy('line')
            ->get()
            ->keyBy('line');
        
        // Combine all lines with breakdown data (including 0 counts)
        $topBreakdownLine = $allLines->map(function($line) use ($breakdownByLine) {
            $breakdown = $breakdownByLine->get($line->name);
            return (object)[
                'line' => $line->name,
                'breakdown_count' => $breakdown ? $breakdown->breakdown_count : 0,
                'total_downtime_min' => $breakdown ? $breakdown->total_downtime_min : 0
            ];
        })->sortByDesc('breakdown_count');
        
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

        // MTBF Metrics dari Machine Model
        $machines = Machine::where('status', 'active')->with('line')->get();
        $mtbfData = [];
        $totalMTBFHours = 0;
        $mtbfMachineCount = 0;

        foreach ($machines as $machine) {
            // Use calculateMTBFAllTime() if all_time is selected, otherwise use calculateMTBF() for specific period
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

        // Sort by MTBF descending
        usort($mtbfData, function ($a, $b) {
            return $b['mtbf_hours'] <=> $a['mtbf_hours'];
        });

        // Average MTBF dari actual calculation
        $avgMTBFHours = $mtbfMachineCount > 0 ? $totalMTBFHours / $mtbfMachineCount : 0;

        // Get top machines by reliability
        $topReliableMachines = array_slice($mtbfData, 0, 5);
        $worstMachines = array_slice(array_reverse($mtbfData), 0, 5);

        // Get downtime by scope (Electrical, Mechanical, Utility, Building)
        $downtimeByScope = $this->getDowntimeByScope($tahun, $bulan, function() use ($baseQuery) {
            return $baseQuery();
        });

        return view('dashboard.department-head', compact(
            'totalLaporan',
            'totalDowntime',
            'totalDowntimeMinutes',
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
            'totalChangeOver',
            'bulan',
            'tahun',
            'mesin',
            'line',
            'allMesins',
            'allLines',
            'mtbfData',
            'avgMTBFHours',
            'topReliableMachines',
            'worstMachines',
            'showAllTime',
            'downtimeByScope',
            'filterMode',
            'dariTanggal',
            'sampaiTanggal'
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
            $q = LaporanHarian::whereYear('tanggal_laporan', $tahun)
                ->whereMonth('tanggal_laporan', $bulan);

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
        
        // Total Downtime (menit) = Corrective + Preventive Maintenance downtime
        // Revision: Downtime includes both corrective and preventive maintenance
        $totalDowntimeCorrective = $baseQuery()->where('jenis_pekerjaan', 'corrective')->where('downtime_min', '>', 0)->sum('downtime_min') ?? 0;
        $totalDowntimePreventive = $baseQuery()->where('jenis_pekerjaan', 'preventive')->where('downtime_min', '>', 0)->sum('downtime_min') ?? 0;
        $totalDowntime = $totalDowntimeCorrective + $totalDowntimePreventive;
        
        // Average MTTR (Mean Time To Repair) - rata-rata dari laporan corrective yang punya downtime
        $avgMTTR = $baseQuery()->where('jenis_pekerjaan', 'corrective')->where('downtime_min', '>', 0)
            ->avg('downtime_min') ?? 0;
        
        // Average MTBF will be calculated from Machine model
        $avgMTBF = 0;
        
        // Machine Performance Metrics
        // Calculate Planned time from database if available, otherwise calculate automatically
        // REVISION: Planned Time can be set manually by administrator
        
        // For specific month, check database first, then fallback to calculation
        $plannedTimeRecord = \App\Models\PlannedTime::where('year', $tahun)
            ->where('month', $bulan)
            ->first();
        
        if ($plannedTimeRecord) {
            // Use manually entered planned time
            $totalPlannedTime = $plannedTimeRecord->planned_time_minutes;
        } else {
            // Fallback: calculate from days in month and active machines
            $daysInMonth = \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;
            
            // Get count of active machines
            $activeMachinesQuery = Machine::where('status', 'active');
            if ($mesin) {
                $activeMachinesQuery->where('name', $mesin);
            }
            $activeMachinesCount = max(1, $activeMachinesQuery->count());
            
            // Formula: days × 24 hours × 60 minutes × number of active machines
            $totalPlannedTime = $daysInMonth * 24 * 60 * $activeMachinesCount;
        }
        
        // Total Breakdown = jumlah laporan corrective dengan downtime
        // (Breakdown = failure events, separate from downtime hours)
        $totalBreakdown = $baseQuery()->where('jenis_pekerjaan', 'corrective')->where('downtime_min', '>', 0)->count();
        
        // Total Downtime Minutes = Corrective + Preventive (calculated above)
        $totalDowntimeMinutes = $totalDowntime;
        
        // Ensure values are positive and valid
        $totalPlannedTime = max(0, $totalPlannedTime);
        $totalDowntimeMinutes = max(0, $totalDowntimeMinutes);
        
        // Hitung Availability dan Downtime Percentage dengan benar
        $downtimePercent = $totalPlannedTime > 0 ? ($totalDowntimeMinutes / $totalPlannedTime) * 100 : 0;
        $downtimePercent = min(100, $downtimePercent); // Cap at 100%
        $availability = 100 - $downtimePercent;
        
        // Maintenance Types (Convert menit to jam)
        // Corrective = downtime_min dari laporan jenis_pekerjaan = 'corrective'
        $totalCorrectiveMaint = ($baseQuery()->where('jenis_pekerjaan', 'corrective')->sum('downtime_min') ?? 0) / 60;
        
        // Preventive = downtime_min dari laporan jenis_pekerjaan = 'preventive'
        $totalPreventiveMaint = ($baseQuery()->where('jenis_pekerjaan', 'preventive')->sum('downtime_min') ?? 0) / 60;
        
        // Change Over Product = downtime_min dari laporan jenis_pekerjaan = 'change over product'
        $totalChangeOver = ($baseQuery()->where('jenis_pekerjaan', 'change over product')->sum('downtime_min') ?? 0) / 60;
        
        // Top 10 Mesin dengan downtime terbanyak
        $topDowntimeMesin = $baseQuery()->select('mesin_name', DB::raw('SUM(downtime_min) as total_downtime'))
            ->groupBy('mesin_name')
            ->orderByDesc('total_downtime')
            ->limit(10)
            ->get();
        
        // All Breakdown by Line (showing all lines including those with 0 breakdown count)
        $allLines = Line::get();
        $breakdownByLine = $baseQuery()
            ->select('line', 
                DB::raw('COUNT(*) as breakdown_count'),
                DB::raw('SUM(downtime_min) as total_downtime_min'))
            ->groupBy('line')
            ->get()
            ->keyBy('line');
        
        // Combine all lines with breakdown data (including 0 counts)
        $topBreakdownLine = $allLines->map(function($line) use ($breakdownByLine) {
            $breakdown = $breakdownByLine->get($line->name);
            return (object)[
                'line' => $line->name,
                'breakdown_count' => $breakdown ? $breakdown->breakdown_count : 0,
                'total_downtime_min' => $breakdown ? $breakdown->total_downtime_min : 0
            ];
        })->sortByDesc('breakdown_count');
        
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

        // Get downtime by scope (Electrical, Mechanical, Utility, Building)
        $downtimeByScope = $this->getDowntimeByScope($tahun, $bulan, function() use ($baseQuery) {
            return $baseQuery();
        });

        return view('dashboard.supervisor', compact(
            'totalLaporan',
            'totalDowntime',
            'totalDowntimeMinutes',
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
            'totalChangeOver',
            'bulan',
            'tahun',
            'mesin',
            'line',
            'allMesins',
            'allLines',
            'downtimeByScope'
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
