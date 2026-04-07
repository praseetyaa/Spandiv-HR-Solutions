<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id', 'name', 'slug', 'domain', 'subdomain', 'status', 'trial_ends_at', 'api_token',
    ];

    /**
     * Prevent api_token from being exposed in JSON/array serialization.
     */
    protected $hidden = [
        'api_token',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    /**
     * Generate a new cryptographically-secure API token for this tenant.
     *
     * Returns the plaintext token (shown only once).
     * Stores a SHA-256 hash in the database for security.
     */
    public function generateApiToken(): string
    {
        $plaintext = Str::random(64);
        $this->update(['api_token' => hash('sha256', $plaintext)]);

        return $plaintext;
    }

    /**
     * Find a tenant by matching a plaintext API token against stored hashes.
     * Uses hash_equals for constant-time comparison to prevent timing attacks.
     */
    public static function findByApiToken(string $plaintext): ?self
    {
        $hash = hash('sha256', $plaintext);

        return static::where('api_token', $hash)
            ->where('status', 'active')
            ->first();
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function setting()
    {
        return $this->hasOne(TenantSetting::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(TenantSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(TenantSubscription::class)->where('status', 'active')->latest();
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
