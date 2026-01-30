<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanHarian extends Model
{
    protected $table = 'laporan_harian';
    
    protected $fillable = [
        'user_id',
        'machine_id',
        'line_id',
        'spare_part_id',
        'mesin_name',
        'line',
        'catatan',
        'sparepart',
        'qty_sparepart',
        'komentar_sparepart',
        'status',
        'jenis_pekerjaan',
        'scope',
        'start_time',
        'end_time',
        'downtime_min',
        'tipe_laporan',
        'tanggal_laporan',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'tanggal_laporan' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }

    // Accessor untuk calculate downtime otomatis berdasarkan start_time dan end_time
    public function getDowntimeMinAttribute($value)
    {
        // Jika jenis_pekerjaan adalah corrective dan ada start_time dan end_time
        if ($this->jenis_pekerjaan === 'corrective' && $this->start_time && $this->end_time) {
            $start = \Carbon\Carbon::parse($this->start_time);
            $end = \Carbon\Carbon::parse($this->end_time);
            return (int) $start->diffInMinutes($end);
        }
        return $value;
    }

    // Mutator untuk set downtime when saving
    public function setDowntimeMinAttribute($value)
    {
        // Jika jenis_pekerjaan adalah corrective, hitung dari start_time dan end_time
        if ($this->jenis_pekerjaan === 'corrective' && isset($this->attributes['start_time']) && isset($this->attributes['end_time'])) {
            $start = \Carbon\Carbon::parse($this->attributes['start_time']);
            $end = \Carbon\Carbon::parse($this->attributes['end_time']);
            $this->attributes['downtime_min'] = (int) $start->diffInMinutes($end);
        } else {
            // Untuk non-corrective, set ke 0
            $this->attributes['downtime_min'] = 0;
        }
    }
}
