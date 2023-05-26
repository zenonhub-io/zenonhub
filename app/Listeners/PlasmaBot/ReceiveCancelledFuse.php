<?php

namespace App\Listeners\PlasmaBot;

use App;
use App\Events\Nom\AccountBlockProcessed;
use App\Models\Nom\Account;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class ReceiveCancelledFuse implements ShouldQueue
{
    public $delay = 10;

    public $tries = 5;

    /**
     * Handle the event.
     */
    public function handle(AccountBlockProcessed $event): void
    {
        if (
            $event->block->account->address === Account::ADDRESS_PLASMA &&
            $event->block->to_account->address === config('plasma-bot.address')
        ) {
            Log::info('Plasma bot - receiving all transactions');
            $plasmaBot = App::make(\App\Services\PlasmaBot::class);
            $plasmaBot->receiveAll();
        }
    }
}
