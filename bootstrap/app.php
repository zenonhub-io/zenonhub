<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/**
 * The first thing we will do is create a new Laravel application instance
 * which serves as the brain for all of the Laravel components. We will
 * also use the application to configure core, foundational behavior.
 */

return Application::configure()
    ->withProviders()
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        // health: '/up',
    )
    ->withCommands([
        __DIR__ . '/../app/Domains/Common/Console/Commands',
        __DIR__ . '/../app/Domains/Indexer/Console/Commands',
        __DIR__ . '/../app/Domains/Nom/Console/Commands',
    ])
    ->withEvents(discover: [
        __DIR__ . '/../app/Domains/Common/Listeners',
        __DIR__ . '/../app/Domains/Indexer/Listeners',
        __DIR__ . '/../app/Domains/Nom/Listeners',
    ])
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
