<?php

declare(strict_types=1);

namespace App\Domains\Common\Listeners;

use App\Bots\BridgeAlertBot;
use App\Bots\WhaleAlertBot;
use App\Domains\Common\Actions\ProcessBlockRewards;
use App\Domains\Common\Actions\ProcessLiquidityProgramRewards;
use App\Domains\Indexer\Events\AccountBlockInserted;
use App\Domains\Nom\Actions\UpdateAccountTotals;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Notifications\Bots\BridgeAlert;
use App\Notifications\Bots\WhaleAlert;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class AccountBlockInsertedListener
{
    use AsAction;

    /**
     * Handle the event.
     */
    public function handle(AccountBlock $accountBlock): void
    {
        //$this->dispatchAccountTotalsProcessor($accountBlock->account);
        //$this->dispatchAccountTotalsProcessor($accountBlock->toAccount);
        $this->dispatchBlockRewardProcessor($accountBlock);
        $this->dispatchLiquidityProgramProcessor($accountBlock);
        //$this->dispatchWhaleAlerts($accountBlock);
        //$this->dispatchBridgeAlerts($accountBlock);
    }

    public function asListener(AccountBlockInserted $accountBlockInsertedEvent): void
    {
        $this->handle($accountBlockInsertedEvent->accountBlock);
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

    private function dispatchBlockRewardProcessor(AccountBlock $accountBlock): void
    {
        if (! $accountBlock->parent || ! $accountBlock->contractMethod) {
            return;
        }

        if ($accountBlock->contractMethod->name !== 'Mint' || $accountBlock->contractMethod->contract->name !== 'Token') {
            return;
        }

        ProcessBlockRewards::dispatch($accountBlock);
    }

    private function dispatchLiquidityProgramProcessor(AccountBlock $accountBlock): void
    {
        if ($accountBlock->token?->token_standard !== NetworkTokensEnum::QSR->value) {
            return;
        }

        if ($accountBlock->account->address !== config('explorer.liquidity_program_distributor')) {
            return;
        }

        ProcessLiquidityProgramRewards::dispatch($accountBlock);
    }

    private function dispatchWhaleAlerts(AccountBlock $accountBlock): void
    {
        $znnValue = config('bots.whale-alerts.znn_cutoff') * NOM_DECIMALS;
        $qsrValue = config('bots.whale-alerts.qsr_cutoff') * NOM_DECIMALS;

        if (! $accountBlock->token) {
            return;
        }

        if (
            ($accountBlock->token->token_standard === NetworkTokensEnum::ZNN->value && $accountBlock->amount >= $znnValue) ||
            ($accountBlock->token->token_standard === NetworkTokensEnum::QSR->value && $accountBlock->amount >= $qsrValue)
        ) {
            Notification::send(new WhaleAlertBot, (new WhaleAlert($accountBlock)));
        }
    }

    private function dispatchBridgeAlerts(AccountBlock $accountBlock): void
    {
        $watchAddresses = config('bots.bridge-alerts.watch_addresses');
        $watchMethods = config('bots.bridge-alerts.watch_methods');

        if (! in_array($accountBlock->toAccount->address, [
            EmbeddedContractsEnum::BRIDGE->value,
            EmbeddedContractsEnum::LIQUIDITY->value,
        ], true)) {
            return;
        }

        if (! in_array($accountBlock->account->address, $watchAddresses, true)) {
            return;
        }

        if (! in_array($accountBlock->contractMethod->name, $watchMethods, true)) {
            return;
        }

        Notification::send(new BridgeAlertBot, (new BridgeAlert($accountBlock)));
    }
}
