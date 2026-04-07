<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    protected $fillable = [
        'candidate_id', 'interviewer_id', 'scheduled_at', 'duration_minutes',
        'type', 'location', 'meeting_url', 'result', 'score', 'notes',
    ];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime'];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'phone'  => 'Telepon',
            'video'  => 'Video Call',
            'onsite' => 'On-Site',
            'panel'  => 'Panel',
            default  => $this->type,
        };
    }

    public function getResultLabelAttribute(): string
    {
        return match ($this->result) {
            'passed'  => 'Lulus',
            'failed'  => 'Tidak Lulus',
            'pending' => 'Menunggu',
            default   => '-',
        };
    }
}
