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
     * Handle the event.
     */
    public function handle(AccountBlockInserted $event): void
    {
        $accountBlock = $event->accountBlock;

        if ($accountBlock->account->address !== config('explorer.empty_address')) {
            $delay = $accountBlock->account->is_embedded_contract
                ? now()->addMinutes(5)->diffInSeconds(now())
                : now()->addMinute()->diffInSeconds(now());
            UpdateAccountTotals::dispatch($accountBlock->account)->delay($delay);
        }

        if ($accountBlock->toAccount->address !== config('explorer.empty_address')) {
            $delay = $accountBlock->toAccount->is_embedded_contract
                ? now()->addMinutes(5)->diffInSeconds(now())
                : now()->addMinute()->diffInSeconds(now());
            UpdateAccountTotals::dispatch($accountBlock->toAccount)->delay($delay);
        }

        ProcessLiquidityProgramRewards::run($accountBlock);
    }
}
