<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceHistory extends Model
{
    protected $fillable = [

        'complaint_id',

        'user_id',

        'event_type',

        'title',

        'description',

        'old_status',

        'new_status',

        'metadata',

        'event_at',
    ];

    protected $casts = [

        'metadata' => 'array',

        'event_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Complaint
    |--------------------------------------------------------------------------
    */

    public function complaint()
    {
        return $this->belongsTo(
            Complaint::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    public static function record(
        Complaint $complaint,
        string $eventType,
        string $title,
        ?string $description = null,
        ?string $oldStatus = null,
        ?string $newStatus = null,
        ?int $userId = null,
        ?array $metadata = null
    ): self {
        return self::create([

            'complaint_id' => $complaint->id,

            'user_id' => $userId
                ?? auth()->id(),

            'event_type' => $eventType,

            'title' => $title,

            'description' => $description,

            'old_status' => $oldStatus,

            'new_status' => $newStatus,

            'metadata' => $metadata,

            'event_at' => now(),
        ]);
    }
}
