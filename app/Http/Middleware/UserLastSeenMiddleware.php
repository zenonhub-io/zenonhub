<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserLastSeenMiddleware
{
    public function handle(Request $request, Closure $next): Response|JsonResponse|RedirectResponse
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $cacheKey = 'user_last_seen_' . $user->id;

        if (Cache::has($cacheKey)) {
            return $next($request);
        }

        $user->last_seen_at = now();
        $user->save();

        Cache::put($cacheKey, now()->toIso8601String(), 60 * 5);

        return $next($request);
    }
}
