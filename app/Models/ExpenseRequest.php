<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseRequest extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'title', 'total_amount', 'expense_date',
        'purpose', 'status', 'approved_by', 'approved_at', 'paid_at', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'expense_date' => 'date',
            'approved_at'  => 'datetime',
            'paid_at'      => 'datetime',
        ];
    }

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function items(): HasMany { return $this->hasMany(ExpenseItem::class, 'request_id'); }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft'    => 'default',
            'pending'  => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'paid'     => 'info',
            default    => 'default',
        };
    }
}
