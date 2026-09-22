<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function complaintTypes(): HasMany
    {
        return $this->hasMany(
            ComplaintCategory::class,
            'division_id'
        );
    }


    public function complaints(): HasMany
    {
        return $this->hasMany(
            Complaint::class,
            'division_id'
        );
    }
}
