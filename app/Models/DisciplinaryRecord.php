<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinaryRecord extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'type', 'level', 'violation',
        'action_taken', 'incident_date', 'issued_by', 'attachment_path',
        'warning_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'warning_expires_at' => 'date',
        ];
    }

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function issuedBy(): BelongsTo { return $this->belongsTo(User::class, 'issued_by'); }

    public function getLevelLabelAttribute(): string
    {
        return match ($this->level) {
            'verbal' => '🗣️ Teguran Lisan',
            'sp1' => '⚠️ SP-1',
            'sp2' => '🔶 SP-2',
            'sp3' => '🔴 SP-3',
            default => $this->level,
        };
    }

    public function getLevelColorAttribute(): string
    {
        return match ($this->level) {
            'verbal' => 'default',
            'sp1' => 'warning',
            'sp2' => 'danger',
            'sp3' => 'danger',
            default => 'default',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'warning' => 'Peringatan',
            'suspension' => 'Skorsing',
            'termination' => 'PHK',
            default => $this->type,
        };
    }

    public function getIsActiveAttribute(): bool
    {
        if (!$this->warning_expires_at) return true;
        return $this->warning_expires_at->isFuture();
    }
}
