<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateTestSession extends Model
{
    protected $fillable = [
        'assignment_id',
        'attempt_number',
        'started_at',
        'finished_at',
        'time_spent_seconds',
        'ip_address',
        'user_agent',
        'browser_fingerprint',
        'is_tab_switched',
        'tab_switch_count',
        'is_completed',
        'finish_method',
    ];

    protected function casts(): array
    {
        return [
            'started_at'      => 'datetime',
            'finished_at'     => 'datetime',
            'is_tab_switched' => 'boolean',
            'is_completed'    => 'boolean',
        ];
    }

    // ============================================================
    // Relationships
    // ============================================================

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(CandidateTestAssignment::class, 'assignment_id');
    }

    // ============================================================
    // Accessors
    // ============================================================

    public function getDurationFormattedAttribute(): string
    {
        if (!$this->time_spent_seconds) return '-';
        $mins = intdiv($this->time_spent_seconds, 60);
        $secs = $this->time_spent_seconds % 60;
        return "{$mins}m {$secs}s";
    }
}
