<?php

namespace App\Listeners\PlasmaBot;

use App;
use App\Events\Nom\Plasma\CancelFuse;
use App\Models\Nom\Account;

class ReceiveCancelledFuse
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        // ...
    }

    /**
     * Handle the event.
     */
    public function handle(CancelFuse $event): void
    {
        if (
            $event->block->account->address !== Account::ADDRESS_PLASMA &&
            $event->block->to_account->address !== config('plasma-bot.address')
        ) {
            return;
        }

        App::make(App\Services\ZnnCli::class)->receive($event->block->hash);
    }
}
