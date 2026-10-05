<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommercialResolution extends Model
{
    protected $fillable = [
        'complaint_id',
        'processed_by',
        'started_at',
        'findings',
        'resolution_remarks',
        'initial_processing_completed_at',
        'forwarded_to_maintenance_at',
        'forwarded_by',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'initial_processing_completed_at' => 'datetime',
        'forwarded_to_maintenance_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(
            Complaint::class,
            'complaint_id'
        );
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'processed_by'
        );
    }

    public function forwarder(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'forwarded_by'
        );
    }
}
