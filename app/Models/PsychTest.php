<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PsychTest extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'category',
        'test_type',
        'description',
        'instructions',
        'duration_minutes',
        'total_questions',
        'passing_score',
        'is_randomize_q',
        'is_randomize_opt',
        'is_tenant_specific',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'passing_score'     => 'decimal:2',
            'is_randomize_q'    => 'boolean',
            'is_randomize_opt'  => 'boolean',
            'is_tenant_specific' => 'boolean',
        ];
    }

    // ============================================================
    // Relationships
    // ============================================================

    public function sections(): HasMany
    {
        return $this->hasMany(PsychTestSection::class, 'test_id')->orderBy('order_number');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'test_id')->orderBy('order_number');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(PsychoTestRequirement::class, 'test_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CandidateTestAssignment::class, 'test_id');
    }

    // ============================================================
    // Scopes
    // ============================================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ============================================================
    // Accessors
    // ============================================================

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'personality'  => '🧠 Personality',
            'intelligence' => '💡 Intelligence',
            'arithmetic'   => '🔢 Arithmetic',
            'sjt'          => '🤝 SJT',
            'projective'   => '🎨 Projective',
            default        => $this->category,
        };
    }
}
