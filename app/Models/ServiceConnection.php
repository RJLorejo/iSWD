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

        'connection_type',

        'meter_size',

        'status',

        'installation_date',

        'remarks',

    ];

    protected $casts = [

        'installation_date' => 'date',

    ];

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    public function address()
    {
        return $this->hasOne(ServiceAddress::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Auto Number
    |--------------------------------------------------------------------------
    */

    public static function generateConnectionNumber(): string
    {
        $next = self::max('id') + 1;

        return 'SC-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
