<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestDimensionNorm extends Model
{
    protected $fillable = [
        'test_id',
        'dimension_key',
        'dimension_label',
        'score_min',
        'score_max',
        'grade',
        'label',
        'description',
        'development_notes',
    ];

    protected function casts(): array
    {
        return [
            'score_min' => 'decimal:2',
            'score_max' => 'decimal:2',
        ];
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(PsychTest::class, 'test_id');
    }
}
