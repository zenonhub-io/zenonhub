<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Listeners;

use App\Domains\Indexer\Events\AccountBlockInserted;
use App\Domains\Nom\Actions\ProcessLiquidityProgramRewards;
use App\Domains\Nom\Actions\UpdateAccountTotals;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Factories\ContractMethodProcessorFactory;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccountBlockInsertedListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(AccountBlockInserted $event): void
    {
        $accountBlock = $event->accountBlock;

        $this->dispatchAccountTotalsProcessor($accountBlock->account);
        $this->dispatchAccountTotalsProcessor($accountBlock->toAccount);
        $this->dispatchLiquidityProgramProcessor($accountBlock);
        //$this->dispatchContractMethodProcessor($accountBlock);
    }

    private function dispatchAccountTotalsProcessor(Account $account): void
    {
        if ($account->address === config('explorer.empty_address')) {
            return;
        }

        $delay = $account->is_embedded_contract
            ? now()->addMinutes(5)
            : now()->addMinutes(1);

        UpdateAccountTotals::dispatch($account)
            ->delay($delay->diffInSeconds(now()));
    }

    private function dispatchLiquidityProgramProcessor(AccountBlock $accountBlock): void
    {
        if ($accountBlock->token?->token_standard !== NetworkTokensEnum::QSR->value) {
            return;
        }

        if ($accountBlock->account->address !== config('explorer.liquidity_program_distributor')) {
            return;
        }

        ProcessLiquidityProgramRewards::run($accountBlock);
    }

    private function dispatchContractMethodProcessor(AccountBlock $accountBlock): void
    {
        if (! $accountBlock->contractMethod) {
            return;
        }

        try {
            $blockProcessorClass = ContractMethodProcessorFactory::create($accountBlock->contractMethod);
            $blockProcessorClass::dispatch($accountBlock);
        } catch (Exception $exception) {
        }
    }
}
