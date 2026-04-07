<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOption extends Model
{
    protected $fillable = [
        'question_id',
        'content',
        'image_path',
        'is_correct',
        'score_value',
        'dimension_key',
        'order_number',
    ];

    protected function casts(): array
    {
        return [
            'is_correct'  => 'boolean',
            'score_value' => 'decimal:2',
        ];
    }

    // ============================================================
    // Relationships
    // ============================================================

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
