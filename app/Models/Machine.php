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
     * MTBF = Total Waktu Operasi (Downtime) / Jumlah Kegagalan (Corrective Failures)
     * 
     * @return array
     */
    public function calculateMTBF()
    {
        // Get all corrective maintenance records for this machine
        $correctiveReports = $this->laporan()
            ->where('jenis_pekerjaan', 'corrective')
            ->get();

        $failureCount = $correctiveReports->count();

        // Calculate total downtime in hours
        $totalDowntimeMinutes = $correctiveReports->sum('downtime_min');
        $totalDowntimeHours = $totalDowntimeMinutes / 60;

        // Calculate MTBF
        $mtbf = $failureCount > 0 ? $totalDowntimeHours / $failureCount : 0;

        return [
            'machine_id' => $this->id,
            'machine_name' => $this->name,
            'failure_count' => $failureCount,
            'total_downtime_minutes' => $totalDowntimeMinutes,
            'total_downtime_hours' => round($totalDowntimeHours, 2),
            'mtbf_hours' => round($mtbf, 2),
            'mtbf_days' => round($mtbf / 24, 2),
        ];
    }
}
