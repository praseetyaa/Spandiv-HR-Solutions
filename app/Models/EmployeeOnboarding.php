<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeOnboarding extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'template_id', 'start_date',
        'expected_end_date', 'progress_percent', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date'        => 'date',
            'expected_end_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class, 'template_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(EmployeeOnboardingTask::class, 'onboarding_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'not_started' => 'Belum Mulai',
            'in_progress' => 'Berjalan',
            'completed'   => 'Selesai',
            'overdue'     => 'Terlambat',
            default       => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'not_started' => 'slate',
            'in_progress' => 'blue',
            'completed'   => 'emerald',
            'overdue'     => 'red',
            default       => 'slate',
        };
    }
}
