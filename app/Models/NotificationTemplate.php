<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'event_key', 'channel', 'subject',
        'body_template', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getChannelLabelAttribute(): string
    {
        return match ($this->channel) {
            'email'    => '📧 Email',
            'whatsapp' => '💬 WhatsApp',
            'in_app'   => '🔔 In-App',
            default    => $this->channel,
        };
    }
}
