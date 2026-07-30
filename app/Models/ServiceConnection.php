<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceConnection extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'consumer_id',

        'account_number',

        'service_connection_number',

        'meter_number',

        'status',

        'installation_date',

        'latitude',

        'longitude'

    ];

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    public function address()
    {
        return $this->hasOne(ServiceAddress::class);
    }
}
