<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consumer extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'user_id',

        'first_name',

        'middle_name',

        'last_name',

        'contact_number',

        'email',

        'valid_id_type',

        'valid_id_number',

        'is_verified'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceConnections()
    {
        return $this->hasMany(ServiceConnection::class);
    }
}
