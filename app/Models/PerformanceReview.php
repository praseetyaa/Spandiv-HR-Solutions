<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerformanceReview extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'cycle_id',
        'employee_id',
        'reviewer_id',
        'reviewer_type',
        'final_score',
        'rating',
        'summary',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'final_score'  => 'decimal:2',
            'submitted_at' => 'datetime',
        ];
    }

    // ============================================================
    // Relationships
    // ============================================================

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(ReviewCycle::class, 'cycle_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function idpPlans(): HasMany
    {
        return $this->hasMany(IdpPlan::class, 'review_id');
    }
}
