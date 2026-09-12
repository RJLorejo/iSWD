<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consumer extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'account_number',

        'user_id',

        'first_name',
        'middle_name',
        'last_name',
        'suffix',

        'sex',

        'phone',
        'email',

        'is_active',
    ];

    protected $casts = [


        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | User Account
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Address
    |--------------------------------------------------------------------------
    */

    public function address()
    {
        return $this->hasOne(
            ConsumerAddress::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Complaints
    |--------------------------------------------------------------------------
    */

    public function complaints()
    {
        return $this->hasMany(
            Complaint::class,
            'consumer_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Display Name
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return trim(
            $this->first_name . ' ' .
                ($this->middle_name
                    ? $this->middle_name . ' '
                    : '') .
                $this->last_name .
                ($this->suffix
                    ? ' ' . $this->suffix
                    : '')
        );
    }
}
