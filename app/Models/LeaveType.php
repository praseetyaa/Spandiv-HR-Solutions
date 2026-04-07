<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveType extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'name', 'code', 'days_per_year', 'is_paid',
        'carry_over', 'max_carry_days', 'requires_attachment',
        'min_notice_days', 'max_consecutive_days', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_paid'             => 'boolean',
            'carry_over'          => 'boolean',
            'requires_attachment' => 'boolean',
            'is_active'           => 'boolean',
        ];
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
