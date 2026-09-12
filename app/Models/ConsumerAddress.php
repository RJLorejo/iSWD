<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumerAddress extends Model
{
    protected $fillable = [

        'consumer_id',

        'house_no',
        'street',
        'purok',
        'barangay',
        'municipality',
        'province',
        'zip_code',
    ];

    protected $casts = [

        'created_at' => 'datetime',
        'updated_at' => 'datetime',

    ];

    /*
    |--------------------------------------------------------------------------
    | Consumer
    |--------------------------------------------------------------------------
    */

    public function consumer()
    {
        return $this->belongsTo(
            Consumer::class,
            'consumer_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Full Address
    |--------------------------------------------------------------------------
    */

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->house_no,
            $this->street,
            $this->purok,
            $this->barangay,
            $this->municipality,
            $this->province,
        ])
            ->filter()
            ->implode(', ');
    }
}
