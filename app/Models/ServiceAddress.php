<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAddress extends Model
{
    protected $fillable = [

        'service_connection_id',

        'house_no',

        'street',

        'purok',

        'barangay',

        'city',

        'province',

        'zip_code',

        'landmark',

    ];

    public function serviceConnection()
    {
        return $this->belongsTo(ServiceConnection::class);
    }

    public function getFullAddressAttribute()
    {
        return collect([

            $this->house_no,

            $this->street,

            $this->purok,

            $this->barangay,

            $this->city,

            $this->province,

        ])->filter()->implode(', ');
    }
}
