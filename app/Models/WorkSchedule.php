<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkSchedule extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'name', 'type', 'start_time', 'end_time',
        'late_tolerance_min', 'work_days', 'is_default',
    ];

    protected function casts(): array
    {
        return [
            'work_days'   => 'array',
            'is_default'  => 'boolean',
        ];
    }

    public function employeeSchedules(): HasMany
    {
        return $this->hasMany(EmployeeSchedule::class, 'schedule_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'fixed'    => 'Tetap',
            'flexible' => 'Fleksibel',
            'shift'    => 'Shift',
            default    => $this->type,
        };
    }
}
