<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'leave_type_id', 'start_date', 'end_date',
        'total_days', 'reason', 'attachment_path', 'status',
        'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date'  => 'date',
            'end_date'    => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Menunggu',
            'approved'  => 'Disetujui',
            'rejected'  => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'amber',
            'approved'  => 'emerald',
            'rejected'  => 'red',
            'cancelled' => 'slate',
            default     => 'slate',
        };
    }
}
