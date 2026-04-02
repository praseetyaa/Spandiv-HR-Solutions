<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'tenant_id', 'name', 'email', 'password', 'guard', 'is_active',
        'last_login_at', 'email_verified_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime',
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Determine guard name for spatie/permission.
     * Platform users use 'platform' guard, web users use 'web'.
     */
    public function getGuardNameAttribute(): string
    {
        return $this->attributes['guard'] ?? 'web';
    }

    // ============================================================
    // Relationships
    // ============================================================

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    // ============================================================
    // Scopes
    // ============================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePlatformOnly($query)
    {
        return $query->where('guard', 'platform');
    }

    public function scopeTenantOnly($query)
    {
        return $query->where('guard', 'web')->whereNotNull('tenant_id');
    }

    // ============================================================
    // Helpers
    // ============================================================

    public function isPlatformUser(): bool
    {
        return $this->guard === 'platform';
    }

    public function isSuperAdmin(): bool
    {
        return $this->isPlatformUser() && $this->hasRole('super_admin');
    }
}
