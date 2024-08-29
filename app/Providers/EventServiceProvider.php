<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Indexer\AccountBlockInserted;
use App\Listeners\AccountBlockInsertedListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Registers the non-discoverable event listeners.
     */
    public function boot(): void
    {
        Event::listen(
            AccountBlockInserted::class,
            AccountBlockInsertedListener::class
        );
    }
}
