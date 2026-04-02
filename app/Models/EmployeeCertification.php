<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeCertification extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'certification_id', 'name', 'issuing_body',
        'certificate_number', 'issued_date', 'expires_date', 'file_path', 'status',
    ];

    protected function casts(): array
    {
        return [
            'issued_date'  => 'date',
            'expires_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function certification(): BelongsTo
    {
        return $this->belongsTo(Certification::class);
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->status === 'active'
            && $this->expires_date
            && $this->expires_date->isBetween(now(), now()->addDays(30));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_date?->isPast() ?? false;
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expires_date) return null;
        return (int) now()->diffInDays($this->expires_date, false);
    }
}
