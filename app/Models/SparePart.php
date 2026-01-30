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
        'status',
    ];

    public function laporan()
    {
        return $this->hasMany(LaporanHarian::class, 'spare_part_id');
    }
}
