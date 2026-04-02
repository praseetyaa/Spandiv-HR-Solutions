<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureEnabled
{
    /**
     * Handle an incoming request.
     * Check if specific module/feature is enabled for the tenant's plan.
     *
     * Usage in routes: ->middleware('feature:recruitment')
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        // Platform users bypass feature check
        if (! $user || $user->isPlatformUser()) {
            return $next($request);
        }

        $tenant = $user->tenant;

        if (! $tenant) {
            abort(403, 'Tenant tidak ditemukan.');
        }

        // Check if feature is enabled in tenant's plan
        $subscription = $tenant->subscriptions()
            ->where('status', 'active')
            ->with('plan')
            ->first();

        if (! $subscription || ! $subscription->plan) {
            abort(403, 'Tidak ada langganan aktif.');
        }

        $enabledFeatures = $subscription->plan->features ?? [];

        if (! in_array($feature, $enabledFeatures)) {
            abort(403, "Fitur '{$feature}' tidak tersedia dalam paket Anda. Silakan upgrade.");
        }

        return $next($request);
    }
}
