<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Indexer\Accelerator\PhaseCreated;
use App\Events\Indexer\Accelerator\ProjectCreated;
use App\Events\Indexer\AccountBlockInserted;
use App\Events\Indexer\Bridge\TokenUnwraped;
use App\Events\Indexer\Bridge\UnwrapRedeemed;
use App\Events\Indexer\Plasma\StartFuse;
use App\Events\Indexer\Token\TokenMinted;
use App\Listeners\AccountBlockInsertedListener;
use App\Listeners\Notifications\Accelerator\PhaseCreatedListener;
use App\Listeners\Notifications\Accelerator\ProjectCreatedListener;
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
        $this->registerIndexerListener();
        $this->registerRewardListener();
        $this->registerPlasmaBotListener();
        $this->registerBridgeListener();
        $this->registerNotificationListeners();
    }

    private function registerIndexerListener(): void
    {
        Event::listen(
            AccountBlockInserted::class,
            AccountBlockInsertedListener::class
        );
    }

    private function registerRewardListener(): void
    {
        Event::listen(
            TokenMinted::class,
            TokenMintedListener::class
        );

        Event::listen(
            UnwrapRedeemed::class,
            UnwrapRedeemedListener::class
        );
    }

    private function registerPlasmaBotListener(): void
    {
        Event::listen(
            StartFuse::class,
            StartFuseListener::class
        );
    }

    private function registerBridgeListener(): void
    {
        Event::listen(
            TokenUnwraped::class,
            TokenUnwrapedListener::class
        );
    }

    private function registerNotificationListeners(): void
    {
        Event::listen(
            ProjectCreated::class,
            ProjectCreatedListener::class
        );

        Event::listen(
            PhaseCreated::class,
            PhaseCreatedListener::class
        );
    }
}
