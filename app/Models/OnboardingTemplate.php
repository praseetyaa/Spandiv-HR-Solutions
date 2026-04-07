<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingTemplate extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'name', 'description', 'department_id', 'position_id',
        'employment_type', 'is_default', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active'  => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'position_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(OnboardingTaskTemplate::class, 'template_id');
    }

    public function onboardings(): HasMany
    {
        return $this->hasMany(EmployeeOnboarding::class, 'template_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
