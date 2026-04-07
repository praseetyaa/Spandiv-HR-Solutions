<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosting extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'department_id', 'position_id', 'created_by',
        'title', 'description', 'requirements', 'employment_type',
        'salary_min', 'salary_max', 'openings', 'status', 'close_date',
    ];

    protected function casts(): array
    {
        return ['close_date' => 'date'];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'position_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'job_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'Draft',
            'published' => 'Dipublikasi',
            'closed'    => 'Ditutup',
            'cancelled' => 'Dibatalkan',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'slate',
            'published' => 'emerald',
            'closed'    => 'blue',
            'cancelled' => 'red',
            default     => 'slate',
        };
    }

    public function getEmploymentTypeLabelAttribute(): string
    {
        return match ($this->employment_type) {
            'permanent'  => 'Tetap',
            'contract'   => 'Kontrak',
            'internship' => 'Magang',
            'freelance'  => 'Freelance',
            default      => $this->employment_type,
        };
    }
}
