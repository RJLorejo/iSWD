<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceReport extends Model
{
    protected $fillable = [
        'complaint_id',
        'technician_id',
        'diagnosis',
        'root_cause',
        'work_performed',
        'repair_procedure',
        'materials_used',
        'parts_replaced',
        'tools_used',
        'technician_notes',
        'completion_remarks',
        'before_photo',
        'after_photo',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Complaint
    |--------------------------------------------------------------------------
    */

    public function complaint()
    {
        return $this->belongsTo(
            Complaint::class,
            'complaint_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Technician
    |--------------------------------------------------------------------------
    */

    public function technician()
    {
        return $this->belongsTo(
            User::class,
            'technician_id'
        );
    }
}
