<?php

declare(strict_types=1);

namespace App\Domains\Nom\Actions;

use App\Domains\Nom\Models\Account;
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

        $this->account = $account->refresh()->load('latestBlock');

        $this->saveCurrentBalance();
        $this->saveStakedZnn();
        $this->saveFusedQsr();
        $this->saveRewardTotals();

        $this->account->updated_at = $this->account->latestBlock?->created_at ?? now();
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
        $accountTokenIds = $this->account->balances()->pluck('token_id');
        $tokenIds = $this->account->receivedBlocks()
            ->whereNotNull('token_id')
            ->distinct()
            ->get(['token_id'])
            ->pluck('token_id');

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

            if ($tokenId === 1) {
                $balance += $this->account->genesis_znn_balance;
                $this->account->znn_balance = $balance;
                $this->account->znn_sent = $sent ?: 0;
                $this->account->znn_received = $received ?: 0;
            }

            if ($tokenId === 2) {
                $balance += $this->account->genesis_qsr_balance;
                $this->account->qsr_balance = $balance;
                $this->account->qsr_sent = $sent ?: 0;
                $this->account->qsr_received = $received ?: 0;
            }

            if ($accountTokenIds->contains($tokenId)) {
                $this->account->balances()->attach($tokenId, [
                    'balance' => $balance,
                    'updated_at' => now(),
                ]);
            } else {
                $this->account->balances()->updateExistingPivot($tokenId, [
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