<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingTaskTemplate extends Model
{
    protected $table = 'onboarding_tasks_template';

    protected $fillable = [
        'template_id', 'title', 'description', 'category',
        'due_day_offset', 'is_required', 'assigned_to_role',
        'notify_to', 'order_number',
    ];

    protected function casts(): array
    {
        return [
            'notify_to'   => 'array',
            'is_required' => 'boolean',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class, 'template_id');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'document'       => '📄 Dokumen',
            'system_access'  => '🔑 Akses Sistem',
            'introduction'   => '🤝 Perkenalan',
            'training'       => '📚 Pelatihan',
            'administrative' => '📋 Administratif',
            default          => $this->category,
        };
    }
}
