<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantScope
{
    /**
     * Handle an incoming request.
     * Resolve tenant from authenticated user and set global context.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Platform users (super admin / support) bypass tenant scope
        if ($user->isPlatformUser()) {
            return $next($request);
        }

        // Regular users must belong to a tenant
        if (! $user->tenant_id) {
            abort(403, 'Akun tidak terkait dengan tenant manapun.');
        }

        // Store tenant_id in a singleton for global access
        app()->instance('current_tenant_id', $user->tenant_id);

        // Share tenant info with all views
        $tenant = $user->tenant;
        view()->share('currentTenant', $tenant);

        return $next($request);
    }
}
