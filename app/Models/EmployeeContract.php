<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeContract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id', 'contract_type', 'start_date', 'end_date',
        'is_probation', 'probation_months', 'probation_end_date',
        'file_path', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date'        => 'date',
            'end_date'          => 'date',
            'probation_end_date' => 'date',
            'is_probation'      => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->contract_type) {
            'pkwt'       => 'PKWT (Kontrak)',
            'pkwtt'      => 'PKWTT (Tetap)',
            'internship' => 'Magang',
            'freelance'  => 'Freelance',
            default      => $this->contract_type,
        };
    }

    public function getIsActiveAttribute(): bool
    {
        return !$this->end_date || $this->end_date->isFuture();
    }

    public function scopeActive($query)
    {
        return $query->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()));
    }
}
