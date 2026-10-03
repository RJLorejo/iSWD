<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintFeedback extends Model
{
    protected $table = 'complaint_feedback';

    protected $fillable = [
        'complaint_id',
        'consumer_id',
        'overall_rating',
        'service_quality_rating',
        'response_time_rating',
        'personnel_courtesy_rating',
        'resolution_status',
        'comments',
        'handling_status',
        'reviewed_by',
        'reviewed_at',
        'follow_up_notes',
        'follow_up_at',
        'resolved_at',
    ];

    protected $casts = [
        'overall_rating' => 'integer',
        'service_quality_rating' => 'integer',
        'response_time_rating' => 'integer',
        'personnel_courtesy_rating' => 'integer',
        'reviewed_at' => 'datetime',
        'follow_up_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function consumer(): BelongsTo
    {
        return $this->belongsTo(Consumer::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getAverageRatingAttribute(): float
    {
        return round(
            (
                $this->overall_rating +
                $this->service_quality_rating +
                $this->response_time_rating +
                $this->personnel_courtesy_rating
            ) / 4,
            1
        );
    }

    public function getNeedsAttentionAttribute(): bool
    {
        return in_array(
            $this->handling_status,
            [
                'Follow-up Required',
                'Follow-up In Progress',
            ],
            true
        );
    }

    public function getIsNewAttribute(): bool
    {
        return $this->handling_status === 'New';
    }

    public function getIsResolvedAttribute(): bool
    {
        return $this->handling_status === 'Resolved';
    }
}
