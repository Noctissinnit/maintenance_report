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
}
