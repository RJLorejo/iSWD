<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceAnnouncement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'content',
        'affected_barangay',
        'start_at',
        'end_at',
        'status',
        'published_by',
        'published_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'published_by'
        );
    }

    public function reads(): HasMany
    {
        return $this->hasMany(
            AnnouncementRead::class,
            'service_announcement_id'
        );
    }

    public function isReadBy(?Consumer $consumer): bool
    {
        if (!$consumer) {
            return false;
        }

        if ($this->relationLoaded('reads')) {
            return $this->reads->contains(
                'consumer_id',
                $consumer->id
            );
        }

        return $this->reads()
            ->where('consumer_id', $consumer->id)
            ->exists();
    }

    public function isActive(): bool
    {
        if ($this->status !== 'Published') {
            return false;
        }

        $now = now();

        if (
            $this->start_at &&
            $now->lt($this->start_at)
        ) {
            return false;
        }

        if (
            $this->end_at &&
            $now->gt($this->end_at)
        ) {
            return false;
        }

        return true;
    }

    public function scopePublished($query)
    {
        return $query
            ->where('status', 'Published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeActive($query)
    {
        $now = now();

        return $query
            ->published()
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')
                    ->orWhere(
                        'start_at',
                        '<=',
                        $now
                    );
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')
                    ->orWhere(
                        'end_at',
                        '>=',
                        $now
                    );
            });
    }
}
