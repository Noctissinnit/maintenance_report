<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    protected $fillable = ['name', 'code', 'description', 'status'];

    public function machines()
    {
        return $this->hasMany(Machine::class);
    }
}
