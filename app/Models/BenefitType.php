<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BenefitType extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'name', 'category', 'description', 'is_mandatory', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_mandatory' => 'boolean', 'is_active' => 'boolean'];
    }

    public function plans(): HasMany
    {
        return $this->hasMany(BenefitPlan::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'insurance' => '🛡️ Asuransi',
            'bpjs'      => '🏥 BPJS',
            'allowance' => '💰 Tunjangan',
            'facility'  => '🏢 Fasilitas',
            default     => '📋 Lainnya',
        };
    }

    public function scopeActive($query) { return $query->where('is_active', true); }
}
