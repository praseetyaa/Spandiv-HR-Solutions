<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BonusScheme extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'name', 'type', 'percentage', 'fixed_amount',
        'period', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function employeeBonuses(): HasMany
    {
        return $this->hasMany(EmployeeBonus::class, 'scheme_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'fixed'             => 'Nominal Tetap',
            'percentage'        => 'Persentase',
            'performance_based' => 'Berbasis Performa',
            default             => $this->type,
        };
    }

    public function getPeriodLabelAttribute(): string
    {
        return match ($this->period) {
            'monthly'   => 'Bulanan',
            'quarterly' => 'Kuartalan',
            'annually'  => 'Tahunan',
            'one_time'  => 'Sekali',
            default     => $this->period,
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
