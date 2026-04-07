<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceCorrection extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'attendance_id', 'employee_id', 'date',
        'corrected_clock_in', 'corrected_clock_out', 'reason',
        'attachment_path', 'status', 'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'date'                => 'date',
            'corrected_clock_in'  => 'datetime',
            'corrected_clock_out' => 'datetime',
            'approved_at'         => 'datetime',
        ];
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
