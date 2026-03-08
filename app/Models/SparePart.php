<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'category',
        'stock',
        'unit',
        'notes',
        'status',
    ];

    public function laporan()
    {
        return $this->hasMany(LaporanHarian::class, 'spare_part_id');
    }
}
