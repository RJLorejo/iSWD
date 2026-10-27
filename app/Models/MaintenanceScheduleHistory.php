<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceScheduleHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'maintenance_work_order_id',
        'performed_by',
        'action',
        'old_values',
        'new_values',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(
            MaintenanceWorkOrder::class,
            'maintenance_work_order_id'
        );
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
