<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintAiAnalysis extends Model
{
    protected $fillable = [

        'complaint_id',

        'predicted_category_id',
        'predicted_type',

        'confidence',
        'confidence_level',
        'confidence_gap',
        'ambiguous',

        'urgency_level',
        'urgency_score',

        'signals',
        'evidence',

        'review_reasons',
        'verification_questions',

        'consumer_accepted',
        'consumer_category_id',
        'verified_category_id',

        'verified_by',
        'verified_at',
        'final_category_id',

        'raw_analysis',

        'analyzed_at',
    ];


    protected $casts = [

        'confidence' => 'float',

        'confidence_gap' => 'float',

        'ambiguous' => 'boolean',

        'urgency_score' => 'integer',

        'signals' => 'array',

        'evidence' => 'array',

        'review_reasons' => 'array',

        'verification_questions' => 'array',

        'consumer_accepted' => 'boolean',
        'verified_at' => 'datetime',

        'raw_analysis' => 'array',

        'analyzed_at' => 'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | Complaint
    |--------------------------------------------------------------------------
    */

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(
            Complaint::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AI Predicted Category
    |--------------------------------------------------------------------------
    */

    public function predictedCategory(): BelongsTo
    {
        return $this->belongsTo(
            ComplaintCategory::class,
            'predicted_category_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Final Human-Selected Category
    |--------------------------------------------------------------------------
    */

    public function finalCategory(): BelongsTo
    {
        return $this->belongsTo(
            ComplaintCategory::class,
            'final_category_id'
        );
    }

    public function consumerCategory(): BelongsTo
    {
        return $this->belongsTo(
            ComplaintCategory::class,
            'consumer_category_id'
        );
    }


    public function verifiedCategory(): BelongsTo
    {
        return $this->belongsTo(
            ComplaintCategory::class,
            'verified_category_id'
        );
    }


    public function verifier(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'verified_by'
        );
    }
}
