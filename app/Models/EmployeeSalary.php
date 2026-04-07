<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSalary extends Model
{
    use HasTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'employee_id', 'grade_id', 'basic_salary',
        'effective_date', 'status', 'approved_by',
    ];

    protected function casts(): array
    {
        return ['effective_date' => 'date'];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class, 'grade_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
