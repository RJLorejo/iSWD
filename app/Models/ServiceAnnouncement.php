<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    /**
     * User who published the announcement.
     */
    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    /**
     * Determine whether the announcement is currently active.
     */
    public function isActive(): bool
    {
        if ($this->status !== 'Published') {
            return false;
        }

        $now = now();

        if ($this->start_at && $now->lt($this->start_at)) {
            return false;
        }

        if ($this->end_at && $now->gt($this->end_at)) {
            return false;
        }

        return true;
    }

    /**
     * Scope to published announcements.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'Published');
    }

    /**
     * Scope to currently active announcements.
     */
    public function scopeActive($query)
    {
        $now = now();

        return $query
            ->where('status', 'Published')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')
                    ->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')
                    ->orWhere('end_at', '>=', $now);
            });
    }
}
