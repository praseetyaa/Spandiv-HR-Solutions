<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grievance extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'category', 'description', 'priority',
        'status', 'is_anonymous', 'assigned_to', 'resolution', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'resolved_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'default',
            'medium' => 'info',
            'high' => 'warning',
            'critical' => 'danger',
            default => 'default',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => '🟢 Open',
            'investigating' => '🔍 Investigasi',
            'resolved' => '✅ Selesai',
            'closed' => '🔒 Ditutup',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'info',
            'investigating' => 'warning',
            'resolved' => 'success',
            'closed' => 'default',
            default => 'default',
        };
    }
}
