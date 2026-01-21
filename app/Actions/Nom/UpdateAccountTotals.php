<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use App\Models\Nom\Account;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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

        DB::transaction(function () use ($account) {

            $this->account = $account->refresh();

            $this->saveStakedZnn();
            $this->saveFusedQsr();
            $this->savePlasmaAmount();
            $this->saveRewardTotals();

            $this->account->save();
        });
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

    private function savePlasmaAmount(): void
    {
        $this->account->plasma_amount = $this->account->plasma()
            ->where('to_account_id', $this->account->id)
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
