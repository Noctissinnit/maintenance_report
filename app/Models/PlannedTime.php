<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlannedTime extends Model
{
    protected $fillable = [
        'year',
        'month',
        'planned_time_minutes',
        'description',
        'created_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'planned_time_minutes' => 'integer',
    ];

    /**
     * Get the user who created this planned time record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to get planned time for specific year and month
     */
    public function scopeForMonth($query, $year, $month)
    {
        return $query->where('year', $year)->where('month', $month)->first();
    }

    /**
     * Get planned time in hours for display
     */
    public function getPlannedTimeHoursAttribute()
    {
        return $this->planned_time_minutes / 60;
    }
}

