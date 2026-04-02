<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TalentProfile extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'potential_level',
        'performance_level',
        'flight_risk',
        'is_successor_ready',
        'strengths',
        'development_notes',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_successor_ready' => 'boolean',
        ];
    }

    // ============================================================
    // Relationships
    // ============================================================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ============================================================
    // 9-Box Grid Helpers
    // ============================================================

    /**
     * Map potential_level to Y axis (0-2) for 9-box grid.
     * 0 = bottom (low), 2 = top (high/very_high)
     */
    public function getGridYAttribute(): int
    {
        return match ($this->potential_level) {
            'low'       => 0,
            'medium'    => 1,
            'high', 'very_high' => 2,
            default     => 1,
        };
    }

    /**
     * Map performance_level to X axis (0-2) for 9-box grid.
     * 0 = left (below), 2 = right (outstanding/exceeds)
     */
    public function getGridXAttribute(): int
    {
        return match ($this->performance_level) {
            'below'     => 0,
            'meets'     => 1,
            'exceeds', 'outstanding' => 2,
            default     => 1,
        };
    }

    /**
     * Get the 9-box cell label.
     */
    public function getNineBoxLabelAttribute(): string
    {
        $labels = [
            '0-0' => 'Risk', '1-0' => 'Average', '2-0' => 'Strong Performer',
            '0-1' => 'Inconsistent', '1-1' => 'Core Player', '2-1' => 'High Performer',
            '0-2' => 'Enigma', '1-2' => 'Growth Potential', '2-2' => 'Star',
        ];

        return $labels["{$this->grid_x}-{$this->grid_y}"] ?? 'Unknown';
    }

    /**
     * Get cell color class for 9-box.
     */
    public function getNineBoxColorAttribute(): string
    {
        $colors = [
            '0-0' => 'red', '1-0' => 'amber', '2-0' => 'blue',
            '0-1' => 'orange', '1-1' => 'slate', '2-1' => 'sky',
            '0-2' => 'purple', '1-2' => 'teal', '2-2' => 'emerald',
        ];

        return $colors["{$this->grid_x}-{$this->grid_y}"] ?? 'slate';
    }

    // ============================================================
    // Scopes
    // ============================================================

    public function scopeByDepartment($query, ?int $departmentId)
    {
        if ($departmentId) {
            return $query->whereHas('employee', fn ($q) => $q->where('department_id', $departmentId));
        }

        return $query;
    }
}
