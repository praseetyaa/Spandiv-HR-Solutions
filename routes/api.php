<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobPostingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are prefixed with /api and do not use session/auth middleware.
| All endpoints require a valid X-API-TOKEN header tied to a specific tenant.
|
| CORS is handled by Laravel's built-in HandleCors middleware (config/cors.php).
| Rate limiting: 60 requests per minute per IP address.
|
*/

// Secured API v1 endpoints
Route::middleware(['throttle:60,1', \App\Http\Middleware\EnsureApiToken::class])
    ->prefix('v1')
    ->group(function () {
        Route::get('/jobs', [JobPostingController::class, 'index']);
        Route::get('/jobs/{slug}', [JobPostingController::class, 'show']);
    });
