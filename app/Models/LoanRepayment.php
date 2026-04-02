<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanRepayment extends Model
{
    protected $fillable = [
        'loan_id', 'payroll_id', 'installment_number', 'amount', 'payment_date', 'status',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'payment_date' => 'date'];
    }

    public function loan(): BelongsTo { return $this->belongsTo(EmployeeLoan::class, 'loan_id'); }
}
