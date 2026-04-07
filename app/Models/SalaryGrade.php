<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryGrade extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'name', 'code', 'level', 'description', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function bands(): HasMany
    {
        return $this->hasMany(SalaryBand::class, 'grade_id');
    }

    public function employeeSalaries(): HasMany
    {
        return $this->hasMany(EmployeeSalary::class, 'grade_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
