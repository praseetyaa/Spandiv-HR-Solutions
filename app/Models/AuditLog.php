<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasTenant;

    public $timestamps = false;

    protected $fillable = [
        'tenant_id', 'user_id', 'action', 'model_type',
        'model_id', 'old_values', 'new_values',
        'ip_address', 'user_agent', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => '🟢 Created',
            'updated' => '🔵 Updated',
            'deleted' => '🔴 Deleted',
            'login'   => '🔑 Login',
            'logout'  => '🚪 Logout',
            default   => $this->action,
        };
    }
}
