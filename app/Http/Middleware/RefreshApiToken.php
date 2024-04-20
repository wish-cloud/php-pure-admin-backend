<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->currentAccessToken()->expires_at->gt(now()) && auth()->user()->currentAccessToken()->expires_at->lt(now()->addMinutes(config('auth.expiration', 60 * 24 * 7) / 2))) {
            auth()->user()->currentAccessToken()->expires_at = now()->addMinutes(config('auth.expiration', 60 * 24 * 7));
            auth()->user()->currentAccessToken()->save();
        }

        return $next($request);
    }
}
