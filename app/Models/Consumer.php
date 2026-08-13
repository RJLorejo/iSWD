<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consumer extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'consumer_no',

        'user_id',

        'first_name',

        'middle_name',

        'last_name',

        'suffix',

        'sex',

        'birth_date',

        'phone',

        'email',

        'is_active',

    ];

    protected $casts = [

        'birth_date' => 'date',

        'is_active' => 'boolean',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->hasOne(ConsumerAddress::class);
    }

    public function serviceConnections()
    {
        return $this->hasMany(ServiceConnection::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute()
    {
        return trim(

            "{$this->first_name} {$this->middle_name} {$this->last_name} {$this->suffix}"

        );
    }

    /*
    |--------------------------------------------------------------------------
    | Consumer Number Generator
    |--------------------------------------------------------------------------
    */

    public static function generateConsumerNo(): string
    {
        $next = self::max('id') + 1;

        return 'CON-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
