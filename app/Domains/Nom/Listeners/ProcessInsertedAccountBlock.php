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

        //(new UpdateAccountTotals)->execute($accountBlock->account);
        //(new UpdateAccountTotals)->execute($accountBlock->toAccount);
        (new ProcessLiquidityProgramRewards)->execute($accountBlock);
    }
}
