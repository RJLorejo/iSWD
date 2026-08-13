<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'complaint_no',

        // Consumer / complainant
        'consumer_id',
        'complainant_name',
        'complainant_phone',

        // Complaint classification
        'complaint_category_id',

        // Assignment
        'assigned_to',
        'customer_service_id',

        // Workflow
        'priority',
        'status',

        // Complaint information
        'subject',
        'description',

        // Location
        'address',
        'landmark',
        'latitude',
        'longitude',

        // Evidence
        'photo',

        // Verification
        'verified_by',
        'verified_at',
        'verification_reason',

        // Completion
        'completed_at',
    ];

    protected $casts = [

        'verified_at' => 'datetime',
        'completed_at' => 'datetime',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',

        'latitude' => 'float',
        'longitude' => 'float',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function consumer()
    {
        return $this->belongsTo(
            Consumer::class,
            'consumer_id'
        );
    }

    public function category()
    {
        return $this->belongsTo(
            ComplaintCategory::class,
            'complaint_category_id'
        );
    }

    public function technician()
    {
        return $this->belongsTo(
            User::class,
            'assigned_to'
        );
    }

    public function customerService()
    {
        return $this->belongsTo(
            User::class,
            'customer_service_id'
        );
    }

    public function verifier()
    {
        return $this->belongsTo(
            User::class,
            'verified_by'
        );
    }

    public function maintenanceReport()
    {
        return $this->hasOne(
            MaintenanceReport::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Complaint Number
    |--------------------------------------------------------------------------
    */

    public static function generateComplaintNo(): string
    {
        $next = self::withTrashed()->max('id') + 1;

        return 'CMP-' . str_pad(
            $next,
            6,
            '0',
            STR_PAD_LEFT
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Complainant Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether this complaint was submitted by a walk-in complainant.
     */
    public function isWalkIn(): bool
    {
        return is_null($this->consumer_id);
    }

    /**
     * Determine whether this complaint belongs to a registered consumer.
     */
    public function isRegisteredConsumer(): bool
    {
        return !is_null($this->consumer_id);
    }

    /**
     * Get the complainant's display name.
     */
    public function getComplainantNameAttribute(): ?string
    {
        if ($this->attributes['complainant_name'] ?? null) {
            return $this->attributes['complainant_name'];
        }

        if ($this->consumer) {
            return $this->consumer->full_name;
        }

        return 'Walk-in / Unregistered Complainant';
    }

    /**
     * Get the complainant's display phone number.
     */
    public function getComplainantPhoneAttribute(): ?string
    {
        if ($this->attributes['complainant_phone'] ?? null) {
            return $this->attributes['complainant_phone'];
        }

        return $this->consumer?->phone;
    }


    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    public function isVerified(): bool
    {
        return $this->status === 'Verified';
    }

    public function isAssigned(): bool
    {
        return $this->status === 'Assigned';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'In Progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }

    public function isClosed(): bool
    {
        return $this->status === 'Closed';
    }

    public function isRejected(): bool
    {
        return $this->status === 'Rejected';
    }
}
