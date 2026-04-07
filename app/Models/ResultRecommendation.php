<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultRecommendation extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'result_id',
        'job_id',
        'fit_level',
        'fit_analysis',
        'strengths',
        'development_areas',
        'interview_probes',
        'created_by',
    ];

    public function result(): BelongsTo
    {
        return $this->belongsTo(CandidateTestResult::class, 'result_id');
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class, 'job_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFitLabelAttribute(): string
    {
        return match ($this->fit_level) {
            'high_fit'     => '🟢 High Fit',
            'moderate_fit' => '🟡 Moderate Fit',
            'low_fit'      => '🔴 Low Fit',
            default        => $this->fit_level,
        };
    }
}
