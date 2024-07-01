<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Indexer\Events\AccountBlockInserted;
use App\Domains\Indexer\Listeners\AccountBlockInsertedListener as IndexerAccountBlockInsertedListener;
use App\Domains\Nom\Listeners\AccountBlockInsertedListener as CommonAccountBlockInsertedListener;
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
            IndexerAccountBlockInsertedListener::class,
        );

        Event::listen(
            AccountBlockInserted::class,
            CommonAccountBlockInsertedListener::class,
        );
    }
}
