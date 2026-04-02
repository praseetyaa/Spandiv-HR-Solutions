<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBenefit extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'plan_id', 'effective_date', 'end_date',
        'employee_contribution', 'employer_contribution', 'status',
    ];

    protected function casts(): array
    {
        return [
            'effective_date'        => 'date',
            'end_date'              => 'date',
            'employee_contribution' => 'decimal:2',
            'employer_contribution' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function plan(): BelongsTo { return $this->belongsTo(BenefitPlan::class, 'plan_id'); }

    public function getTotalContributionAttribute(): float
    {
        return $this->employee_contribution + $this->employer_contribution;
    }
}
