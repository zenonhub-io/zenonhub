<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Indexer\AccountBlockInserted;
use App\Events\Indexer\Bridge\TokenUnwraped;
use App\Events\Indexer\Bridge\UnwrapRedeemed;
use App\Events\Indexer\Plasma\StartFuse;
use App\Events\Indexer\Token\TokenMinted;
use App\Listeners\AccountBlockInsertedListener;
use App\Listeners\StartFuseListener;
use App\Listeners\TokenMintedListener;
use App\Listeners\TokenUnwrapedListener;
use App\Listeners\UnwrapRedeemedListener;
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

        Event::listen(
            StartFuse::class,
            StartFuseListener::class
        );

        Event::listen(
            TokenMinted::class,
            TokenMintedListener::class
        );

        Event::listen(
            TokenUnwraped::class,
            TokenUnwrapedListener::class
        );

        Event::listen(
            UnwrapRedeemed::class,
            UnwrapRedeemedListener::class
        );
    }
}
