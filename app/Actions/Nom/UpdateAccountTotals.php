<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use App\Enums\Nom\EmbeddedContractsEnum;
use App\Models\Nom\Account;
use App\Models\Nom\Token;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class UpdateAccountTotals implements ShouldBeUnique
{
    use AsAction;

    public string $commandSignature = 'nom:update-account-totals';

    private Account $account;

    public function getJobUniqueId(Account $account): int
    {
        return $account->id;
    }

    public function handle(Account $account): void
    {
        Log::debug('Update account totals', [
            'address' => $account->address,
        ]);

        $this->account = $account->refresh();

        $this->saveCurrentBalance();
        $this->saveStakedZnn();
        $this->saveFusedQsr();
        $this->saveRewardTotals();

        $this->account->save();
    }

    public function asCommand(Command $command): void
    {
        $totalAccounts = Account::count();
        $progressBar = new ProgressBar(new ConsoleOutput, $totalAccounts);
        $progressBar->start();

        Account::chunk(1000, function (Collection $accounts) use ($progressBar) {
            $accounts->each(function ($account) use ($progressBar) {
                $this->handle($account);
                $progressBar->advance();
            });
        });

        $progressBar->finish();
    }

    private function saveCurrentBalance(): void
    {
        $accountTokenIds = $this->account->tokens()->pluck('token_id');
        $tokenIds = $this->account->blocks()
            ->whereNotNull('token_id')
            ->distinct()
            ->get(['token_id'])
            ->pluck('token_id')
            ->push(app('znnToken')->id, app('qsrToken')->id);
        // Must include znn & qsr token ids above or it doesnt
        // calculate genesis balances correctly

        $tokenIds->each(function ($tokenId) use ($accountTokenIds) {

            $sent = $this->account->sentBlocks()
                ->selectRaw('CAST(SUM(amount) AS INTEGER) as total')
                ->where('token_id', $tokenId)
                ->first()->total;

            $received = $this->account->receivedBlocks()
                ->selectRaw('CAST(SUM(amount) AS INTEGER) as total')
                ->where('token_id', $tokenId)
                ->first()->total;

            $balance = $received - $sent;

            // Token contract behaves differently, it never holds tokens so balance is always 0
            if ($this->account->address === EmbeddedContractsEnum::TOKEN->value) {

                $balance = 0;

                if ($tokenId === app('znnToken')->id) {
                    // Token contract holds 1 znn per token created
                    // Subtract two for the existing ZNN and QSR tokens
                    $tokensCreated = Token::count() - 2;
                    $balance = $tokensCreated * NOM_DECIMALS;
                }
            }

            if ($tokenId === app('znnToken')->id) {
                $balance += $this->account->genesis_znn_balance;
                $this->account->znn_balance = $balance;
                $this->account->znn_sent = $sent ?: 0;
                $this->account->znn_received = $received ?: 0;
            }

            if ($tokenId === app('qsrToken')->id) {
                $balance += $this->account->genesis_qsr_balance;
                $this->account->qsr_balance = $balance;
                $this->account->qsr_sent = $sent ?: 0;
                $this->account->qsr_received = $received ?: 0;
            }

            if ($accountTokenIds->contains($tokenId)) {
                $this->account->tokens()->updateExistingPivot($tokenId, [
                    'balance' => $balance,
                    'updated_at' => now(),
                ]);
            } else {
                $this->account->tokens()->attach($tokenId, [
                    'balance' => $balance,
                    'updated_at' => now(),
                ]);
            }
        });
    }

    private function saveStakedZnn(): void
    {
        $this->account->znn_staked = $this->account->stakes()
            ->whereActive()
            ->whereZnn()
            ->sum('amount');
    }

    private function saveFusedQsr(): void
    {
        $this->account->qsr_fused = $this->account->fusions()
            ->whereActive()
            ->sum('amount');
    }

    private function saveRewardTotals(): void
    {
        $this->account->znn_rewards = $this->account
            ->rewards()
            ->where('token_id', app('znnToken')->id)
            ->sum('amount');

        $this->account->qsr_rewards = $this->account
            ->rewards()
            ->where('token_id', app('qsrToken')->id)
            ->sum('amount');
    }
}
