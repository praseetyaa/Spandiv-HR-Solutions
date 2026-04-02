<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certification extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'name', 'issuing_body', 'validity_months', 'is_mandatory', 'description', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_mandatory' => 'boolean',
            'is_active'    => 'boolean',
        ];
    }

    public function employeeCertifications(): HasMany
    {
        return $this->hasMany(EmployeeCertification::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
