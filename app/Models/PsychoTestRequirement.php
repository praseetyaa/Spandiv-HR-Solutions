<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychoTestRequirement extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'job_id',
        'test_id',
        'is_mandatory',
        'min_passing_score',
        'trigger_stage',
        'deadline_days',
        'order_number',
    ];

    protected function casts(): array
    {
        return [
            'is_mandatory'      => 'boolean',
            'min_passing_score' => 'decimal:2',
        ];
    }

    // ============================================================
    // Relationships
    // ============================================================

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class, 'job_id');
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(PsychTest::class, 'test_id');
    }
}
