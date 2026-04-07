<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PsychTestSection extends Model
{
    protected $fillable = [
        'test_id',
        'name',
        'instruction',
        'order_number',
        'duration_minutes',
        'question_type',
        'is_timed_per_q',
        'questions_to_answer',
    ];

    protected function casts(): array
    {
        return [
            'is_timed_per_q' => 'boolean',
        ];
    }

    // ============================================================
    // Relationships
    // ============================================================

    public function test(): BelongsTo
    {
        return $this->belongsTo(PsychTest::class, 'test_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'section_id')->orderBy('order_number');
    }
}
