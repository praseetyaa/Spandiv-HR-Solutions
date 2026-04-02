<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BenefitPlan extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'benefit_type_id', 'name', 'provider', 'coverage_amount',
        'coverage_type', 'details', 'is_active',
    ];

    protected function casts(): array
    {
        return ['coverage_amount' => 'decimal:2', 'details' => 'array', 'is_active' => 'boolean'];
    }

    public function benefitType(): BelongsTo { return $this->belongsTo(BenefitType::class); }
    public function employeeBenefits(): HasMany { return $this->hasMany(EmployeeBenefit::class, 'plan_id'); }
    public function scopeActive($query) { return $query->where('is_active', true); }
}
