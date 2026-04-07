<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateTestResult extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'assignment_id',
        'session_id',
        'raw_score',
        'scaled_score',
        'percentile',
        'grade',
        'dimension_scores',
        'dimension_grades',
        'auto_analysis',
        'reviewer_notes',
        'overall_recommendation',
        'reviewed_by',
        'reviewed_at',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'raw_score'        => 'decimal:2',
            'scaled_score'     => 'decimal:2',
            'percentile'       => 'decimal:2',
            'dimension_scores' => 'array',
            'dimension_grades' => 'array',
            'reviewed_at'      => 'datetime',
            'is_published'     => 'boolean',
            'published_at'     => 'datetime',
        ];
    }

    // ============================================================
    // Relationships
    // ============================================================

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(CandidateTestAssignment::class, 'assignment_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CandidateTestSession::class, 'session_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ============================================================
    // Accessors
    // ============================================================

    public function getRecommendationLabelAttribute(): string
    {
        return match ($this->overall_recommendation) {
            'highly_recommended' => '⭐ Highly Recommended',
            'recommended'        => '✅ Recommended',
            'not_recommended'    => '❌ Not Recommended',
            'pending'            => '⏳ Pending',
            default              => $this->overall_recommendation ?? 'N/A',
        };
    }

    public function getRecommendationColorAttribute(): string
    {
        return match ($this->overall_recommendation) {
            'highly_recommended' => 'success',
            'recommended'        => 'info',
            'not_recommended'    => 'danger',
            default              => 'warning',
        };
    }
}
