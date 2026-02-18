<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLoyaltyTier
{
    /**
     * Handle an incoming request.
     * Check if user has required loyalty tier for accessing certain features
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $minTier = 'silver'): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access this feature.');
        }

        $user = auth()->user();
        $userLoyalty = $user->loyalty;

        if (!$userLoyalty) {
            return redirect()->route('loyalty.index')->with('error', 'Join our loyalty program to access this feature.');
        }

        $tierLevels = [
            'bronze' => 1,
            'silver' => 2,
            'gold' => 3,
            'platinum' => 4,
        ];

        $userTierLevel = $tierLevels[strtolower($userLoyalty->tier?->slug ?? 'bronze')] ?? 1;
        $requiredLevel = $tierLevels[$minTier] ?? 1;

        if ($userTierLevel < $requiredLevel) {
            return redirect()->route('loyalty.tiers')->with('error', "You need to be at least {$minTier} tier to access this feature.");
        }

        return $next($request);
    }
}
