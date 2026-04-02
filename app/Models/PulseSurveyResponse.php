<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PulseSurveyResponse extends Model
{
    protected $fillable = [
        'survey_id', 'question_id', 'employee_id',
        'answer_text', 'rating_value', 'answered_at',
    ];

    protected function casts(): array
    {
        return ['answered_at' => 'datetime'];
    }

    public function question(): BelongsTo { return $this->belongsTo(PulseSurveyQuestion::class, 'question_id'); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
}
