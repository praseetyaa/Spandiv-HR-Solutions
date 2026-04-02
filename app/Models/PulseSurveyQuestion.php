<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PulseSurveyQuestion extends Model
{
    protected $fillable = [
        'survey_id', 'type', 'content', 'order_number', 'is_required',
    ];

    protected function casts(): array
    {
        return ['is_required' => 'boolean'];
    }

    public function survey(): BelongsTo { return $this->belongsTo(PulseSurvey::class, 'survey_id'); }
    public function responses(): HasMany { return $this->hasMany(PulseSurveyResponse::class, 'question_id'); }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'rating' => '⭐ Rating',
            'multiple_choice' => '📋 Pilihan Ganda',
            'text' => '✏️ Teks',
            'nps' => '📊 NPS',
            default => $this->type,
        };
    }
}
