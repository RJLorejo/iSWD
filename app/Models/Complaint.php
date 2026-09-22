<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'complaint_no',
        'consumer_id',
        'complainant_name',
        'complainant_phone',
        'complaint_category_id',
        'division_id',
        'assigned_to',
        'customer_service_id',
        'priority',
        'status',
        'description',
        'address',
        'landmark',
        'latitude',
        'longitude',
        'photo',
        'verified_by',
        'verified_at',
        'verification_reason',
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

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function technicians()
    {
        return $this->belongsToMany(
            User::class,
            'complaint_technicians',
            'complaint_id',
            'technician_id'
        )->withPivot([
            'status',
            'assigned_at',
            'started_at',
            'completed_at',
        ])->withTimestamps();
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

    public function maintenanceHistories()
    {
        return $this->hasMany(
            MaintenanceHistory::class
        )->latest('event_at');
    }

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

    public function isWalkIn(): bool
    {
        return is_null($this->consumer_id);
    }

    public function isRegisteredConsumer(): bool
    {
        return !is_null($this->consumer_id);
    }

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

    public function getComplainantPhoneAttribute(): ?string
    {
        if ($this->attributes['complainant_phone'] ?? null) {
            return $this->attributes['complainant_phone'];
        }

        return $this->consumer?->phone;
    }

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

    public function isAccomplished(): bool
    {
        return $this->status === 'Accomplished';
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
