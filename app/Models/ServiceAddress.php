<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAddress extends Model
{
    protected $fillable = [

        'service_connection_id',

        'house_no',

        'street',

        'barangay',

        'city',

        'province',

        'postal_code'

    ];

    public function serviceConnection()
    {
        return $this->belongsTo(ServiceConnection::class);
    }
}
