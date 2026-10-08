<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationOtp extends Model
{
    protected $fillable = [
        'user_id',
        'channel',
        'purpose',
        'destination',
        'code_hash',
        'expires_at',
        'attempts',
        'verified_at',
        'last_sent_at',
    ];


    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }


    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }
}
