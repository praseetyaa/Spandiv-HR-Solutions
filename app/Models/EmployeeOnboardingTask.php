<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeOnboardingTask extends Model
{
    protected $fillable = [
        'onboarding_id', 'template_task_id', 'title', 'description',
        'category', 'due_date', 'assigned_to', 'is_completed',
        'completed_at', 'notes', 'attachment_path',
    ];

    protected function casts(): array
    {
        return [
            'due_date'     => 'date',
            'completed_at' => 'datetime',
            'is_completed' => 'boolean',
        ];
    }

    public function onboarding(): BelongsTo
    {
        return $this->belongsTo(EmployeeOnboarding::class, 'onboarding_id');
    }

    public function templateTask(): BelongsTo
    {
        return $this->belongsTo(OnboardingTaskTemplate::class, 'template_task_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getIsOverdueAttribute(): bool
    {
        return !$this->is_completed && $this->due_date?->isPast();
    }
}
