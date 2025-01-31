<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

class InjectLivewire
{
    public function handle(Request $request, Closure $next): Response
    {
        Config::set('livewire.inject_assets', true);

        return $next($request);
    }
}
