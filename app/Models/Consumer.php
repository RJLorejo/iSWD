<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consumer extends Model
{
    use SoftDeletes;

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | Account
        |--------------------------------------------------------------------------
        */

        'account_number',
        'user_id',


        /*
        |--------------------------------------------------------------------------
        | Personal Information
        |--------------------------------------------------------------------------
        */

        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'sex',


        /*
        |--------------------------------------------------------------------------
        | Contact Information
        |--------------------------------------------------------------------------
        */

        'phone',
        'email',


        /*
        |--------------------------------------------------------------------------
        | Verification
        |--------------------------------------------------------------------------
        */

        'verification_status',
        'verified_at',
        'verified_by',
        'verification_reason',

        'email_verified_at',
        'phone_verified_at',

        'registration_source',


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'is_active',
    ];


    protected $casts = [

        'is_active' =>
        'boolean',

        'verified_at' =>
        'datetime',

        'email_verified_at' =>
        'datetime',

        'phone_verified_at' =>
        'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
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

    public function address(): HasOne
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

    public function complaints(): HasMany
    {
        return $this->hasMany(
            Complaint::class,
            'consumer_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Verified By
    |--------------------------------------------------------------------------
    */

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'verified_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Full Name
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return collect([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ])
            ->filter()
            ->implode(' ');
    }


    /*
    |--------------------------------------------------------------------------
    | Verification Helpers
    |--------------------------------------------------------------------------
    */

    public function getIsPendingVerificationAttribute(): bool
    {
        return $this->verification_status
            === 'Pending Verification';
    }


    public function getIsVerifiedAttribute(): bool
    {
        return $this->verification_status
            === 'Verified';
    }


    public function getIsRejectedAttribute(): bool
    {
        return $this->verification_status
            === 'Rejected';
    }
}
