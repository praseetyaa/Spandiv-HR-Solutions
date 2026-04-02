<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceItem extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'name', 'category', 'frequency', 'next_due_date',
        'responsible_id', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return ['next_due_date' => 'date'];
    }

    public function responsible(): BelongsTo { return $this->belongsTo(User::class, 'responsible_id'); }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'completed' => 'success',
            'overdue' => 'danger',
            default => 'default',
        };
    }

    public function getFrequencyLabelAttribute(): string
    {
        return match ($this->frequency) {
            'monthly' => 'Bulanan',
            'quarterly' => 'Triwulan',
            'annually' => 'Tahunan',
            'one_time' => 'Sekali',
            default => $this->frequency,
        };
    }

    public function scopeOverdue($query)
    {
        return $query->where('next_due_date', '<', now())->where('status', '!=', 'completed');
    }
}
