<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        $actual = strtolower(trim((string) $request->user()->role));
        $expected = strtolower(trim((string) $role));

        if ($actual !== $expected) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
