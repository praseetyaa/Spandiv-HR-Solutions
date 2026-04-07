<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'employee_id', 'schedule_id', 'date',
        'clock_in', 'clock_out', 'clock_in_method',
        'clock_in_lat', 'clock_in_lng', 'clock_out_lat', 'clock_out_lng',
        'clock_in_photo', 'status', 'late_minutes', 'early_leave_minutes', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'date'      => 'date',
            'clock_in'  => 'datetime',
            'clock_out' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class, 'schedule_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'present'  => 'Hadir',
            'late'     => 'Terlambat',
            'absent'   => 'Tidak Hadir',
            'leave'    => 'Cuti',
            'holiday'  => 'Libur',
            'half_day' => 'Setengah Hari',
            'wfh'      => 'WFH',
            default    => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'present'  => 'emerald',
            'late'     => 'amber',
            'absent'   => 'red',
            'leave'    => 'blue',
            'holiday'  => 'purple',
            'half_day' => 'orange',
            'wfh'      => 'cyan',
            default    => 'slate',
        };
    }

    public function getWorkHoursAttribute(): ?string
    {
        if (!$this->clock_in || !$this->clock_out) return null;
        $diff = $this->clock_in->diff($this->clock_out);
        return $diff->format('%H:%I');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }
}
