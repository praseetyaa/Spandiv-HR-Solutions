<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSchedule extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'schedule_id',
        'effective_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'end_date'       => 'date',
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
}
