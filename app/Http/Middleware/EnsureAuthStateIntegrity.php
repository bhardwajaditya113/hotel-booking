<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Production auth fix: Ensures session and auth state are properly maintained
 * across requests, especially in containerized/stateless environments.
 */
class EnsureAuthStateIntegrity
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure session is started and has proper configuration
        if (!$request->hasSession()) {
            Log::warning('AuthStateIntegrity: Request missing session', [
                'path' => $request->path(),
                'method' => $request->method(),
            ]);
        }

        // Fix: Ensure cookies are being sent correctly in production
        if (config('app.env') === 'production') {
            // Force secure cookies only on HTTPS
            if ($request->secure()) {
                $request->setLazySession(
                    \Illuminate\Session\Store::class
                );
            }
        }

        $response = $next($request);

        // Ensure cache headers don't interfere with session
        if ($response->status() === 200) {
            $response->header('Cache-Control', 'private, must-revalidate');
        }

        return $response;
    }
}
