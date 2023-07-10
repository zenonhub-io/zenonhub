<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $cacheKey = 'user_last_seen_'.$user->id;

        if (Cache::has($cacheKey)) {
            return $next($request);
        }

        $user->last_seen_at = now();
        $user->save();

        Cache::put($cacheKey, now()->toIso8601String(), 60 * 5);

        return $next($request);
    }
}
