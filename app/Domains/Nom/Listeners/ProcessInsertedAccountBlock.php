<?php

declare(strict_types=1);

namespace App\Domains\Nom\Listeners;

use App\Domains\Nom\Actions\ProcessLiquidityProgramRewards;
use App\Domains\Nom\Actions\UpdateAccountTotals;
use App\Domains\Nom\Events\AccountBlockInserted;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessInsertedAccountBlock implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AccountBlockInserted $event): void
    {
        $accountBlock = $event->accountBlock;

        if ($accountBlock->account->address !== config('explorer.empty_address')) {
            UpdateAccountTotals::dispatch($accountBlock->account)->delay(60);
        }

        if ($accountBlock->toAccount->address !== config('explorer.empty_address')) {
            UpdateAccountTotals::dispatch($accountBlock->toAccount)->delay(60);
        }

        (new ProcessLiquidityProgramRewards)->execute($accountBlock);
    }
}
