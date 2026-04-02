<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyPolicy extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'title', 'category', 'code', 'description',
        'requires_acknowledgment', 'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'requires_acknowledgment' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function versions(): HasMany { return $this->hasMany(PolicyVersion::class, 'policy_id'); }
    public function acknowledgments(): HasMany { return $this->hasMany(PolicyAcknowledgment::class, 'policy_id'); }

    public function currentVersion(): HasOne
    {
        return $this->hasOne(PolicyVersion::class, 'policy_id')->where('is_current', true);
    }

    public function scopeActive($query) { return $query->where('is_active', true); }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'hr' => '👥 HR',
            'legal' => '⚖️ Legal',
            'safety' => '🦺 K3',
            'it' => '💻 IT',
            'finance' => '💰 Finance',
            default => '📋 Umum',
        };
    }
}
