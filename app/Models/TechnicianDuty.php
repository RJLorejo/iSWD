<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnicianDuty extends Model
{
    protected $table = 'technician_duties';

    protected $fillable = [
        'technician_id',
        'duty_date',
        'start_time',
        'end_time',
        'assigned_area',
        'duty_type',
        'status',
    ];

    protected $casts = [
        'duty_date' => 'date',
    ];

    public function technician(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'technician_id'
        );
    }

    public function scopeAvailableOn($query, string $date)
    {
        return $query
            ->whereDate('duty_date', $date)
            ->whereIn('status', [
                'Scheduled',
                'On Duty',
            ]);
    }
}
