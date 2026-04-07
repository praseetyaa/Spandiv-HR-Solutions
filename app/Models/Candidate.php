<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'job_id', 'name', 'email', 'phone',
        'cv_path', 'portfolio_url', 'stage', 'status',
        'score', 'source', 'notes',
    ];

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class, 'job_id');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function stageLogs(): HasMany
    {
        return $this->hasMany(CandidateStageLog::class);
    }

    public function getStageLabelAttribute(): string
    {
        return match ($this->stage) {
            'applied'    => 'Melamar',
            'screening'  => 'Screening',
            'interview'  => 'Interview',
            'offering'   => 'Offering',
            'hired'      => 'Diterima',
            'rejected'   => 'Ditolak',
            default      => $this->stage,
        };
    }

    public function getStageColorAttribute(): string
    {
        return match ($this->stage) {
            'applied'   => 'slate',
            'screening' => 'blue',
            'interview' => 'purple',
            'offering'  => 'amber',
            'hired'     => 'emerald',
            'rejected'  => 'red',
            default     => 'slate',
        };
    }
}
