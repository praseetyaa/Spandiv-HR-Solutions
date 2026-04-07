<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'period_id', 'employee_id', 'gross_salary',
        'total_allowances', 'total_deductions', 'tax_pph21',
        'bpjs_kes_employee', 'bpjs_kes_employer',
        'bpjs_tk_employee', 'bpjs_tk_employer',
        'net_salary', 'status', 'notes',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'period_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'Draft',
            'finalized' => 'Final',
            'paid'      => 'Dibayar',
            default     => $this->status,
        };
    }
}
