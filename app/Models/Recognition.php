<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recognition extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'giver_id', 'receiver_id',
        'badge_type', 'message', 'is_public', 'points',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'points' => 'integer',
        ];
    }

    public function giver(): BelongsTo { return $this->belongsTo(Employee::class, 'giver_id'); }
    public function receiver(): BelongsTo { return $this->belongsTo(Employee::class, 'receiver_id'); }

    public function getBadgeLabelAttribute(): string
    {
        return match ($this->badge_type) {
            'teamwork' => '🤝 Teamwork',
            'innovation' => '💡 Inovasi',
            'leadership' => '👑 Kepemimpinan',
            'dedication' => '💪 Dedikasi',
            'customer_focus' => '🎯 Customer Focus',
            'above_beyond' => '🚀 Above & Beyond',
            default => '⭐ ' . ucfirst($this->badge_type),
        };
    }

    public function getBadgeEmojiAttribute(): string
    {
        return match ($this->badge_type) {
            'teamwork' => '🤝',
            'innovation' => '💡',
            'leadership' => '👑',
            'dedication' => '💪',
            'customer_focus' => '🎯',
            'above_beyond' => '🚀',
            default => '⭐',
        };
    }
}
