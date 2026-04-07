<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryComponent extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'name', 'type', 'calculation_type',
        'default_amount', 'is_taxable', 'is_mandatory', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_taxable'   => 'boolean',
            'is_mandatory' => 'boolean',
            'is_active'    => 'boolean',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'allowance'  => 'Tunjangan',
            'deduction'  => 'Potongan',
            default      => $this->type,
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAllowances($query)
    {
        return $query->where('type', 'allowance');
    }

    public function scopeDeductions($query)
    {
        return $query->where('type', 'deduction');
    }
}
