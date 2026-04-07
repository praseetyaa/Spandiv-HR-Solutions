<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'user_id', 'type', 'title', 'body',
        'data', 'action_url', 'is_read', 'read_at',
    ];

    protected function casts(): array
    {
        return [
            'data'    => 'array',
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
