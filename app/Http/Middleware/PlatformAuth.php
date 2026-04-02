<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlatformAuth
{
    /**
     * Handle an incoming request.
     * Ensure the user is authenticated with 'platform' guard (Super Admin / Support Admin).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->isPlatformUser()) {
            abort(403, 'Akses ditolak. Area ini hanya untuk administrator platform.');
        }

        if (! $user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan.');
        }

        return $next($request);
    }
}
