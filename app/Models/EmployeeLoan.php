<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeLoan extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'loan_amount', 'installment_months',
        'monthly_deduction', 'start_date', 'remaining_amount', 'status',
        'approved_by', 'approved_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'loan_amount'      => 'decimal:2',
            'monthly_deduction' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'start_date'       => 'date',
            'approved_at'      => 'datetime',
        ];
    }

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function repayments(): HasMany { return $this->hasMany(LoanRepayment::class, 'loan_id'); }

    public function getPaidAmountAttribute(): float
    {
        return $this->loan_amount - $this->remaining_amount;
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->loan_amount <= 0) return 0;
        return (int) round(($this->paid_amount / $this->loan_amount) * 100);
    }
}
