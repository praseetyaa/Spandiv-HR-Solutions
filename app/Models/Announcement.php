<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'title', 'content', 'priority',
        'target_departments', 'publish_at', 'expires_at',
        'is_published', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'target_departments' => 'array',
            'publish_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'default',
            'normal' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'default',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'Rendah',
            'normal' => 'Normal',
            'high' => 'Penting',
            'urgent' => '🔴 Urgent',
            default => $this->priority,
        };
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where('publish_at', '<=', now())
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }
}
