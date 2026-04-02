<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSchedule extends Model
{
    protected $fillable = [
        'program_id', 'start_date', 'end_date', 'location', 'mode',
        'meeting_url', 'trainer_id', 'trainer_name', 'available_seats', 'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'program_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(TrainingParticipant::class, 'schedule_id');
    }

    public function getRegisteredCountAttribute(): int
    {
        return $this->participants()->count();
    }

    public function getRemainingSeatsAttribute(): int
    {
        return max(0, $this->available_seats - $this->registered_count);
    }

    public function getModeIconAttribute(): string
    {
        return match ($this->mode) {
            'online'  => '💻',
            'offline' => '🏢',
            'hybrid'  => '🔄',
            default   => '📍',
        };
    }
}
