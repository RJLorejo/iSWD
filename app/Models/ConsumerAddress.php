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

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    public function getFullAddressAttribute()
    {
        return collect([
            $this->house_no,
            $this->street,
            $this->purok,
            $this->barangay,
            $this->municipality,
            $this->province,
            $this->zip_code,
        ])->filter()->implode(', ');
    }
}
