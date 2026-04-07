<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'leave_type_id', 'year',
        'balance', 'used', 'carry_forward',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function getRemainingAttribute(): float
    {
        return $this->balance + $this->carry_forward - $this->used;
    }
}
