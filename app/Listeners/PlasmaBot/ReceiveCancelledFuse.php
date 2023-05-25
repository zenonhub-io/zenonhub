<?php

namespace App\Listeners\PlasmaBot;

use App;
use App\Events\AccountBlockProcessed;
use App\Models\Nom\Account;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class ReceiveCancelledFuse implements ShouldQueue
{
    public $delay = 10;

    /**
     * Handle the event.
     */
    public function handle(AccountBlockProcessed $event): void
    {
        Log::info('Plasma bot - listener firing');
        if (
            $event->block->account->address === Account::ADDRESS_PLASMA &&
            $event->block->to_account->address === config('plasma-bot.address')
        ) {
            Log::info('Plasma bot - receiving all transactions');
            try {
                App::make(App\Services\ZnnCli::class, [
                    'node_url' => config('plasma-bot.node_url'),
                    'keystore' => config('plasma-bot.keystore'),
                    'passphrase' => config('plasma-bot.passphrase'),
                ])->receiveAll();
            } catch (\Throwable $exception) {
                Log::error('Plasma bot - unable to receive transactions - '.$exception->getMessage());
            }
        }
    }
}
