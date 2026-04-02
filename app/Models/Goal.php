<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'cycle_id',
        'title',
        'description',
        'metric_unit',
        'target',
        'actual',
        'weight_percent',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'target'         => 'decimal:2',
            'actual'         => 'decimal:2',
            'weight_percent' => 'integer',
        ];
    }

    // ============================================================
    // Relationships
    // ============================================================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(ReviewCycle::class, 'cycle_id');
    }

    // ============================================================
    // Computed
    // ============================================================

    public function getAchievementPercentAttribute(): float
    {
        if ($this->target <= 0) {
            return 0;
        }

        return round(($this->actual / $this->target) * 100, 1);
    }
}
