<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Models\Nom\AccountBlock;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class ProcessBlockTransfers implements ShouldBeUnique
{
    use AsAction;

    public string $commandSignature = 'nom:process-block-transfers';

    public function getJobUniqueId(AccountBlock $block): int
    {
        return $block->id;
    }

    public function handle(AccountBlock $block): void
    {
        Log::debug('Process block transfers', [
            'hash' => $block->hash,
        ]);

        DB::transaction(function () use ($block) {

            $block
                ->loadMissing(['account', 'toAccount', 'token', 'pairedAccountBlock'])
                ->refresh();

            $sendBlocks = [
                AccountBlockTypesEnum::SEND->value,
                AccountBlockTypesEnum::CONTRACT_SEND->value,
            ];

            $receiveBlocks = [
                AccountBlockTypesEnum::RECEIVE->value,
                AccountBlockTypesEnum::CONTRACT_RECEIVE->value,
            ];

            // The token contract is responsible for minting to we dont want to update the amount it
            // sends or we end up with negative balances
            if (
                $block->account->address !== EmbeddedContractsEnum::TOKEN->value &&
                ($block->amount > 0 && in_array($block->block_type->value, $sendBlocks, true))
            ) {
                $this->updateSendingAccount($block);
            }

            if ($block->pairedAccountBlock?->amount > 0 && in_array($block->block_type->value, $receiveBlocks, true)) {
                $this->updateReceivingAccount($block);
            }
        }, 3);
    }

    public function asCommand(Command $command): void
    {
        $totalBlocks = AccountBlock::count();
        $progressBar = new ProgressBar(new ConsoleOutput, $totalBlocks);
        $progressBar->start();

        AccountBlock::chunk(1000, function (Collection $blocks) use ($progressBar) {
            $blocks->each(function ($block) use ($progressBar) {
                $this->handle($block);
                $progressBar->advance();
            });
        });

        $progressBar->finish();
    }

    private function updateSendingAccount(AccountBlock $block): void
    {
        $account = $block->account;
        $token = $block->token;
        $amount = $block->amount;
        $accountTokens = $account->tokens()->get();

        if ($token->id === app('znnToken')->id) {
            $account->update([
                'znn_balance' => bcsub($account->znn_balance, $amount),
                'znn_sent' => bcadd($account->znn_sent, $amount),
            ]);
        }

        if ($token->id === app('qsrToken')->id) {
            $account->update([
                'qsr_balance' => bcsub($account->qsr_balance, $amount),
                'qsr_sent' => bcadd($account->qsr_sent, $amount),
            ]);
        }

        $holdings = $account->tokens()
            ->where('token_id', $token->id)
            ->first();

        $currentBalance = (string) ($holdings->pivot?->balance ?? 0);
        $newBalance = bcsub($currentBalance, $amount);

        if ($accountTokens->pluck('id')->contains($token->id)) {
            $account->tokens()->updateExistingPivot($token->id, [
                'balance' => $newBalance,
                'updated_at' => $block->created_at,
            ]);
        } else {
            $account->tokens()->attach($token->id, [
                'balance' => $newBalance,
                'updated_at' => $block->created_at,
            ]);
        }
    }

    private function updateReceivingAccount(AccountBlock $block): void
    {
        $account = $block->account;
        $token = $block->pairedAccountBlock->token;
        $amount = $block->pairedAccountBlock->amount;
        $accountTokens = $account->tokens()->get();

        if ($token->id === app('znnToken')->id) {
            $account->update([
                'znn_balance' => bcadd($account->znn_balance, $amount),
                'znn_received' => bcadd($account->znn_received, $amount),
            ]);
        }

        if ($token->id === app('qsrToken')->id) {
            $account->update([
                'qsr_balance' => bcadd($account->qsr_balance, $amount),
                'qsr_received' => bcadd($account->qsr_received, $amount),
            ]);
        }

        $holdings = $account->tokens()
            ->where('token_id', $token->id)
            ->first();

        $currentBalance = (string) ($holdings->pivot?->balance ?? 0);
        $newBalance = bcadd($currentBalance, $amount);

        if ($accountTokens->pluck('id')->contains($token->id)) {
            $account->tokens()->updateExistingPivot($token->id, [
                'balance' => $newBalance,
                'updated_at' => $block->created_at,
            ]);
        } else {
            $account->tokens()->attach($token->id, [
                'balance' => $newBalance,
                'updated_at' => $block->created_at,
            ]);
        }
    }
}
