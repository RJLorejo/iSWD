<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [

        'position_name',

        'description',

        'is_active'

    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
