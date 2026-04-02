<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuccessionPlan extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'position_id',
        'candidate_employee_id',
        'readiness_level',
        'priority',
        'development_notes',
        'created_by',
    ];

    // ============================================================
    // Relationships
    // ============================================================

    public function position(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'position_id');
    }

    public function candidateEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'candidate_employee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ============================================================
    // Helpers
    // ============================================================

    public function getReadinessLabelAttribute(): string
    {
        return match ($this->readiness_level) {
            1 => 'Ready Now',
            2 => 'Ready in 1 Year',
            3 => 'Ready in 2-3 Years',
            4 => 'Ready in 3+ Years',
            5 => 'Not Ready',
            default => 'Unknown',
        };
    }
}
