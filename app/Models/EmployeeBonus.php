<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBonus extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'scheme_id', 'period_id',
        'amount', 'notes', 'status', 'approved_by',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(BonusScheme::class, 'scheme_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'period_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Menunggu',
            'approved' => 'Disetujui',
            'paid'     => 'Dibayar',
            default    => $this->status,
        };
    }
}
