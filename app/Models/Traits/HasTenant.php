<?php

namespace App\Models\Traits;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

trait HasTenant
{
    /**
     * Boot the trait: add global scope and auto-set tenant_id on create.
     */
    public static function bootHasTenant(): void
    {
        // Auto-apply tenant scope to all queries
        static::addGlobalScope(new TenantScope());

        // Auto-set tenant_id when creating a new record
        static::creating(function (Model $model) {
            if (! $model->tenant_id && auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    /**
     * Scope a query to a specific tenant.
     */
    public function scopeForTenant($query, int $tenantId)
    {
        return $query->withoutGlobalScope(TenantScope::class)
                     ->where('tenant_id', $tenantId);
    }

    /**
     * Remove the tenant scope temporarily (e.g., for platform admin).
     */
    public function scopeWithoutTenantScope($query)
    {
        return $query->withoutGlobalScope(TenantScope::class);
    }
}
