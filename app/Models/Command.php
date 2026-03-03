<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Command extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_head_id',
        'title',
        'command_text',
        'action_plan',
        'status',
        'supervisor_id',
        'supervisor_notes',
        'created_date',
        'due_date',
    ];

    protected $casts = [
        'created_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    /**
     * Get the department head who created the command
     */
    public function departmentHead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'department_head_id');
    }

    /**
     * Get the supervisor who is assigned to the command
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
