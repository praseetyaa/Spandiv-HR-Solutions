<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'test_id',
        'section_id',
        'type',
        'content',
        'image_path',
        'order_number',
        'points',
        'time_limit_sec',
        'dimension_key',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'points'    => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // ============================================================
    // Relationships
    // ============================================================

    public function test(): BelongsTo
    {
        return $this->belongsTo(PsychTest::class, 'test_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(PsychTestSection::class, 'section_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'question_id')->orderBy('order_number');
    }
}
