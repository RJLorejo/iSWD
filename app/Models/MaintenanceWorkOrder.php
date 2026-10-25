<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceWorkOrder extends Model
{
    protected $fillable = [
        'maintenance_schedule_id',
        'complaint_id',
        'visit_number',
        'dispatch_type',
        'status',
        'planned_start_time',
        'planned_end_time',
        'scheduled_by',
        'started_at',
        'completed_at',
        'pending_reason',
        'pending_recorded_at',
        'pending_recorded_by',
        'is_late_addition',
        'late_addition_reason',
        'manager_remarks',
        'approval_status',
        'approved_by',
        'approved_at',
        'missed_reason',
        'missed_reported_by',
        'missed_reported_at',
        'missed_confirmed_by',
        'missed_confirmed_at',
        'rescheduled_from_work_order_id',
    ];

    protected function casts(): array
    {
        return [
            'visit_number' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'pending_recorded_at' => 'datetime',
            'is_late_addition' => 'boolean',
            'approved_at' => 'datetime',
            'missed_reported_at' => 'datetime',
            'missed_confirmed_at' => 'datetime',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'maintenance_schedule_id');
    }

    public function technicians(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'maintenance_work_order_technicians',
            'maintenance_work_order_id',
            'technician_id'
        )->withPivot('assignment_role')->withTimestamps();
    }

    public function participations(): HasMany
    {
        return $this->hasMany(MaintenanceWorkOrderParticipation::class, 'maintenance_work_order_id');
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function scheduler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }

    public function pendingRecorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pending_recorded_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(MaintenanceScheduleHistory::class, 'maintenance_work_order_id');
    }

    public function isPendingWorkload(): bool
    {
        return $this->status === 'Pending Workload';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }

    public function isEmergency(): bool
    {
        return $this->dispatch_type === 'Emergency';
    }
}
