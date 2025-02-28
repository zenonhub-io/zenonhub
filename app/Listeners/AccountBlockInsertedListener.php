<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Nom\ProcessLiquidityProgramRewards;
use App\Actions\Nom\UpdateAccountTotals;
use App\Bots\BridgeAlertBot;
use App\Bots\WhaleAlertBot;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Events\Indexer\AccountBlockInserted;
use App\Factories\ContractMethodProcessorFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Notifications\Bots\BridgeAlert;
use App\Notifications\Bots\WhaleAlert;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class AccountBlockInsertedListener
{
    use AsAction;

    /**
     * Handle the event.
     */
    public function handle(AccountBlock $accountBlock): void
    {
        $this->dispatchContractMethodProcessor($accountBlock);
        $this->dispatchAccountTotalsProcessor($accountBlock->account);
        $this->dispatchAccountTotalsProcessor($accountBlock->toAccount);
        $this->dispatchLiquidityProgramProcessor($accountBlock);
        $this->dispatchWhaleAlerts($accountBlock);
        $this->dispatchBridgeAlerts($accountBlock);
    }

    public function asListener(AccountBlockInserted $accountBlockInsertedEvent): void
    {
        $this->handle($accountBlockInsertedEvent->accountBlock);
    }

    private function dispatchContractMethodProcessor(AccountBlock $accountBlock): void
    {
        if (! $accountBlock->contractMethod) {
            return;
        }

        try {
            $jobDelay = now()->addMinute()->diffInSeconds(now());
            $blockProcessorClass = ContractMethodProcessorFactory::create($accountBlock->contractMethod);
            $blockProcessorClass::dispatch($accountBlock)
                ->onQueue('indexer')
                ->delay($jobDelay);
        } catch (Throwable $exception) {
        }
    }

    private function dispatchAccountTotalsProcessor(Account $account): void
    {
        if ($account->address === config('explorer.burn_address')) {
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
        if ($accountBlock->token?->token_standard !== app('qsrToken')->token_standard) {
            return;
        }

        if ($accountBlock->account->address !== config('explorer.liquidity_program_distributor')) {
            return;
        }

        ProcessLiquidityProgramRewards::dispatch($accountBlock);
    }

    private function dispatchWhaleAlerts(AccountBlock $accountBlock): void
    {
        if (! config('bots.whale-alerts.enabled')) {
            return;
        }

        $znnValue = config('bots.whale-alerts.settings.znn_cutoff') * config('nom.decimals');
        $qsrValue = config('bots.whale-alerts.settings.qsr_cutoff') * config('nom.decimals');

        if (! $accountBlock->token) {
            return;
        }

        if (
            ($accountBlock->token->token_standard === app('znnToken')->token_standard && $accountBlock->amount >= $znnValue) ||
            ($accountBlock->token->token_standard === app('qsrToken')->token_standard && $accountBlock->amount >= $qsrValue)
        ) {
            Notification::send(new WhaleAlertBot, (new WhaleAlert($accountBlock)));
        }
    }

    private function dispatchBridgeAlerts(AccountBlock $accountBlock): void
    {
        if (! config('bots.bridge-alerts.enabled')) {
            return;
        }

        $watchAddresses = config('bots.bridge-alerts.settings.watch_addresses');
        $watchMethods = config('bots.bridge-alerts.settings.watch_methods');

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
