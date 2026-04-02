<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class PulseSurvey extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'title', 'description', 'is_anonymous',
        'target_departments', 'start_date', 'end_date', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'target_departments' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function questions(): HasMany { return $this->hasMany(PulseSurveyQuestion::class, 'survey_id'); }
    public function responses(): HasManyThrough { return $this->hasManyThrough(PulseSurveyResponse::class, PulseSurveyQuestion::class, 'survey_id', 'question_id'); }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'default',
            'active' => 'success',
            'closed' => 'info',
            default => 'default',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => '📝 Draft',
            'active' => '🟢 Aktif',
            'closed' => '🔒 Ditutup',
            default => $this->status,
        };
    }

    public function scopeActive($query) { return $query->where('status', 'active'); }
}
