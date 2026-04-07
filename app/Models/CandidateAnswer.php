<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateAnswer extends Model
{
    protected $fillable = [
        'session_id',
        'question_id',
        'selected_option_id',
        'answer_text',
        'number_input',
        'time_spent_sec',
        'is_flagged',
        'answer_order',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'number_input' => 'decimal:2',
            'is_flagged'   => 'boolean',
            'answered_at'  => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CandidateTestSession::class, 'session_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }
}
