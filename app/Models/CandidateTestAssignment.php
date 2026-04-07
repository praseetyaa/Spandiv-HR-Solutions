<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CandidateTestAssignment extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'candidate_id',
        'test_id',
        'assigned_by',
        'access_token',
        'deadline_at',
        'max_attempts',
        'attempt_count',
        'status',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'deadline_at' => 'datetime',
            'notified_at' => 'datetime',
        ];
    }

    // ============================================================
    // Relationships
    // ============================================================

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(PsychTest::class, 'test_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CandidateTestSession::class, 'assignment_id');
    }

    public function result(): HasOne
    {
        return $this->hasOne(CandidateTestResult::class, 'assignment_id');
    }

    // ============================================================
    // Accessors
    // ============================================================

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => '⏳ Pending',
            'in_progress' => '🔄 In Progress',
            'completed'   => '✅ Completed',
            'expired'     => '⏰ Expired',
            default       => $this->status,
        };
    }
}
