<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;

class MTBFController extends Controller
{
    /**
     * Display MTBF analysis for all machines
     */
    public function index()
    {
        // Check permission
        if (!auth()->user()->can('view_own_laporan')) {
            abort(403, 'Unauthorized');
        }

        // Get all active machines with their MTBF
        $machines = Machine::where('status', 'active')
            ->with('line')
            ->get();

        $mtbfData = [];
        $totalFailures = 0;
        $totalDowntime = 0;

        foreach ($machines as $machine) {
            $mtbf = $machine->calculateMTBF();
            $mtbfData[] = $mtbf;
            $totalFailures += $mtbf['failure_count'];
            $totalDowntime += $mtbf['total_downtime_hours'];
        }

        // Sort by MTBF descending (higher MTBF = more reliable)
        usort($mtbfData, function ($a, $b) {
            return $b['mtbf_hours'] <=> $a['mtbf_hours'];
        });

        // Calculate average MTBF
        $machineCount = count($mtbfData);
        $averageMTBFArray = array_filter(array_column($mtbfData, 'mtbf_hours'));
        $averageMTBF = count($averageMTBFArray) > 0 ? array_sum($averageMTBFArray) / count($averageMTBFArray) : 0;
        $averageMTBFDays = $averageMTBF / 24;

        $statistics = [
            'total_machines' => $machineCount,
            'total_failures' => $totalFailures,
            'total_downtime_hours' => round($totalDowntime, 2),
            'average_mtbf_hours' => round($averageMTBF, 2),
            'average_mtbf_days' => round($averageMTBFDays, 2),
        ];

        return view('mtbf.index', [
            'mtbfData' => $mtbfData,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Display MTBF analysis for a specific machine
     */
    public function show($machineId)
    {
        // Check permission
        if (!auth()->user()->can('view_own_laporan')) {
            abort(403, 'Unauthorized');
        }

        $machine = Machine::findOrFail($machineId);
        $mtbfData = $machine->calculateMTBF();

        // Get detailed corrective maintenance history
        $correctiveMaintenance = $machine->laporan()
            ->where('jenis_pekerjaan', 'corrective')
            ->with(['user', 'line'])
            ->orderBy('tanggal_laporan', 'desc')
            ->paginate(10);

        // Get maintenance summary
        $allReports = $machine->laporan()
            ->with(['user', 'line'])
            ->get();

        $maintenanceSummary = [
            'corrective' => $allReports->where('jenis_pekerjaan', 'corrective')->count(),
            'preventive' => $allReports->where('jenis_pekerjaan', 'preventive')->count(),
            'modifikasi' => $allReports->where('jenis_pekerjaan', 'modifikasi')->count(),
            'utility' => $allReports->where('jenis_pekerjaan', 'utility')->count(),
        ];

        return view('mtbf.show', [
            'machine' => $machine,
            'mtbfData' => $mtbfData,
            'correctiveMaintenance' => $correctiveMaintenance,
            'maintenanceSummary' => $maintenanceSummary,
        ]);
    }
}
