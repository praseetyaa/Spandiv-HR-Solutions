<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IdpPlan extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'review_id',
        'year',
        'development_focus',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'year'        => 'integer',
        ];
    }

    // ============================================================
    // Relationships
    // ============================================================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(PerformanceReview::class, 'review_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(IdpActivity::class, 'idp_id');
    }

    // ============================================================
    // Computed
    // ============================================================

    public function getProgressPercentageAttribute(): int
    {
        $total = $this->activities()->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $this->activities()->where('status', 'completed')->count();

        return (int) round(($completed / $total) * 100);
    }

    public function getActivitySummaryAttribute(): array
    {
        return [
            'total'       => $this->activities()->count(),
            'completed'   => $this->activities()->where('status', 'completed')->count(),
            'in_progress' => $this->activities()->where('status', 'in_progress')->count(),
            'planned'     => $this->activities()->where('status', 'planned')->count(),
        ];
    }

    // ============================================================
    // Scopes
    // ============================================================

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
