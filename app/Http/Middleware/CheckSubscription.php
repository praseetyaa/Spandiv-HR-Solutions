<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     * Check if the tenant's subscription is still active.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Platform users bypass subscription check
        if (! $user || $user->isPlatformUser()) {
            return $next($request);
        }

        $tenant = $user->tenant;

        if (! $tenant) {
            abort(403, 'Tenant tidak ditemukan.');
        }

        // Check active subscription
        $subscription = $tenant->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();

        if (! $subscription) {
            // Allow access to billing/subscription pages even when expired
            if ($request->routeIs('billing.*', 'subscription.*')) {
                return $next($request);
            }

            return redirect()->route('subscription.expired')
                ->with('warning', 'Langganan Anda telah berakhir. Silakan perpanjang untuk melanjutkan.');
        }

        // Share subscription info with views
        view()->share('currentSubscription', $subscription);

        return $next($request);
    }
}
