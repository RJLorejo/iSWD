<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceSchedule extends Model
{
    protected $fillable = [
        'schedule_date',
        'status',
        'created_by',
        'finalized_by',
        'finalized_at',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'schedule_date' => 'date',
            'finalized_at' => 'datetime',
        ];
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(MaintenanceWorkOrder::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function finalizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function isFinalized(): bool
    {
        return $this->status === 'Finalized';
    }
}
