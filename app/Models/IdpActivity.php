<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdpActivity extends Model
{
    protected $fillable = [
        'idp_id',
        'activity_type',
        'title',
        'description',
        'target_date',
        'status',
        'outcome',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'target_date'  => 'date',
            'completed_at' => 'datetime',
        ];
    }

    // ============================================================
    // Relationships
    // ============================================================

    public function idpPlan(): BelongsTo
    {
        return $this->belongsTo(IdpPlan::class, 'idp_id');
    }

    // ============================================================
    // Helpers
    // ============================================================

    public function getActivityTypeIconAttribute(): string
    {
        return match ($this->activity_type) {
            'training'      => '🎓',
            'mentoring'     => '🤝',
            'project'       => '📋',
            'course'        => '📚',
            'certification' => '🏅',
            default         => '📌',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'completed'
            && $this->status !== 'cancelled'
            && $this->target_date?->isPast();
    }
}
