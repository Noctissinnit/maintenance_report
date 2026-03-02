<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = [
        'name',
        'code',
        'line_id',
        'description',
        'status',
    ];

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function laporan()
    {
        return $this->hasMany(LaporanHarian::class, 'machine_id');
    }

    /**
     * Calculate MTBF (Mean Time Between Failures) for this machine
     * MTBF = Running Time / Jumlah Kegagalan (Corrective Failures)
     * Running Time = Planned Time - Downtime
     * Planned Time = Hari dalam periode × 24 jam × 60 menit
     * 
     * @param int $tahun - Year (optional, default current year)
     * @param int $bulan - Month (optional, default current month)
     * @return array
     */
    public function calculateMTBF($tahun = null, $bulan = null)
    {
        // Default to current month/year if not provided
        $tahun = $tahun ?? \Carbon\Carbon::now()->year;
        $bulan = $bulan ?? \Carbon\Carbon::now()->month;

        // Get corrective maintenance records for this machine and period
        $correctiveReports = $this->laporan()
            ->where('jenis_pekerjaan', 'corrective')
            ->whereYear('tanggal_laporan', $tahun)
            ->whereMonth('tanggal_laporan', $bulan)
            ->get();

        $failureCount = $correctiveReports->count();

        // Calculate total downtime in minutes for this period
        $totalDowntimeMinutes = $correctiveReports->sum('downtime_min');
        $totalDowntimeHours = round($totalDowntimeMinutes / 60, 2);

        // Calculate Planned Time (hari dalam bulan × 24 jam × 60 menit)
        $daysInMonth = \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;
        $plannedTimeMinutes = $daysInMonth * 24 * 60;
        $plannedTimeHours = round($plannedTimeMinutes / 60, 2);

        // Calculate Running Time (Planned Time - Downtime)
        $runningTimeMinutes = max(0, $plannedTimeMinutes - $totalDowntimeMinutes);
        $runningTimeHours = round($runningTimeMinutes / 60, 2);

        // Calculate MTBF = Running Time / Failure Count
        $mtbf = $failureCount > 0 ? $runningTimeHours / $failureCount : 0;

        return [
            'machine_id' => $this->id,
            'machine_name' => $this->name,
            'failure_count' => $failureCount,
            'planned_time_hours' => $plannedTimeHours,
            'total_downtime_minutes' => $totalDowntimeMinutes,
            'total_downtime_hours' => $totalDowntimeHours,
            'running_time_hours' => $runningTimeHours,
            'mtbf_hours' => round($mtbf, 2),
            'mtbf_days' => round($mtbf / 24, 2),
            'line_name' => $this->line ? $this->line->name : 'Line Tidak Diketahui',
            'tahun' => $tahun,
            'bulan' => $bulan,
        ];
    }

    /**
     * Calculate MTBF (Mean Time Between Failures) for all time (no period filter)
     * MTBF = Running Time / Jumlah Kegagalan (Corrective Failures)
     * Running Time = Total Planned Time (all months) - Downtime
     * 
     * @return array
     */
    public function calculateMTBFAllTime()
    {
        // Get all corrective maintenance records for this machine (all time)
        $correctiveReports = $this->laporan()
            ->where('jenis_pekerjaan', 'corrective')
            ->get();

        $failureCount = $correctiveReports->count();

        // Calculate total downtime in minutes (all time)
        $totalDowntimeMinutes = $correctiveReports->sum('downtime_min');
        $totalDowntimeHours = round($totalDowntimeMinutes / 60, 2);

        // Calculate Planned Time for all months in range
        // Get earliest and latest report dates
        $earliestReport = $this->laporan()->orderBy('tanggal_laporan', 'asc')->first();
        $latestReport = $this->laporan()->orderBy('tanggal_laporan', 'desc')->first();

        $plannedTimeHours = 0;
        if ($earliestReport && $latestReport) {
            $startDate = \Carbon\Carbon::parse($earliestReport->tanggal_laporan);
            $endDate = \Carbon\Carbon::parse($latestReport->tanggal_laporan);
            
            // Count total days from start to end
            $totalDays = $endDate->diffInDays($startDate) + 1;
            $plannedTimeHours = round($totalDays * 24, 2);
        } else {
            // No reports, assume current month
            $daysInMonth = \Carbon\Carbon::now()->daysInMonth;
            $plannedTimeHours = round($daysInMonth * 24, 2);
        }

        // Calculate Running Time (Planned Time - Downtime)
        $runningTimeHours = max(0, round($plannedTimeHours - $totalDowntimeHours, 2));

        // Calculate MTBF = Running Time / Failure Count
        $mtbf = $failureCount > 0 ? $runningTimeHours / $failureCount : 0;

        return [
            'machine_id' => $this->id,
            'machine_name' => $this->name,
            'failure_count' => $failureCount,
            'planned_time_hours' => $plannedTimeHours,
            'total_downtime_minutes' => $totalDowntimeMinutes,
            'total_downtime_hours' => $totalDowntimeHours,
            'running_time_hours' => $runningTimeHours,
            'mtbf_hours' => round($mtbf, 2),
            'mtbf_days' => round($mtbf / 24, 2),
            'line_name' => $this->line ? $this->line->name : 'Line Tidak Diketahui',
            'period' => 'All Time',
        ];
    }
}
