<?php

namespace App\Listeners\PlasmaBot;

use App;
use App\Events\AccountBlockProcessed;
use App\Models\Nom\Account;

class ReceiveCancelledFuse
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(AccountBlockProcessed $event): void
    {
        if (
            $event->block->account->address === Account::ADDRESS_PLASMA &&
            $event->block->to_account->address === config('plasma-bot.address')
        ) {
            App::make(App\Services\ZnnCli::class, [
                'node_url' => config('plasma-bot.node_url'),
                'keystore' => config('plasma-bot.keystore'),
                'passphrase' => config('plasma-bot.passphrase'),
            ])->receive($event->block->hash);
        }
    }
}
