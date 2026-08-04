<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [

        'complaint_no',

        'consumer_id',

        'complaint_category_id',

        'assigned_to',

        'supervisor_id',

        'priority',

        'status',

        'subject',

        'description',

        'address',

        'landmark',

        'latitude',

        'longitude',

        'photo',

        'verified_at',

        'completed_at'

    ];

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
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

    public function supervisor()
    {
        return $this->belongsTo(
            User::class,
            'supervisor_id'
        );
    }
}
