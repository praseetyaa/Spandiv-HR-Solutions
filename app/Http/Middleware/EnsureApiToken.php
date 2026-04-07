<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class EnsureApiToken
{
    /**
     * Handle an incoming request.
     *
     * Validates the API token from the X-API-TOKEN header,
     * resolves the owning tenant, and attaches it to the request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only accept token from header (never query string — logs exposure risk)
        $token = $request->header('X-API-TOKEN');

        if (!$token || strlen($token) < 16) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. API token is missing or malformed.',
            ], 401);
        }

        // Lookup tenant using hashed token (constant-time via hash_equals in SQL)
        $tenant = Tenant::findByApiToken($token);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API token.',
            ], 401);
        }

        // Attach tenant to the request for downstream use
        $request->attributes->set('api_tenant', $tenant);

        $response = $next($request);

        // Cache-Control: allow client-side caching for 5 minutes
        $response->headers->set('Cache-Control', 'public, max-age=300');

        return $response;
    }
}
