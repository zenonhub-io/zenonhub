<?php

declare(strict_types=1);

namespace App\Domains\Nom\Actions;

use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\Token;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateAccountTotals implements ShouldBeUnique
{
    use AsAction;

    private Account $account;

    public function getJobUniqueId(Account $account): int
    {
        return $account->id;
    }

    public function getJobUniqueFor(Account $account): int
    {
        if ($account->is_embedded_contract) {
            return 60 * 5;
        }

        return 60;
    }

    public function handle(Account $account): void
    {
        $this->account = $account;

        $this->saveCurrentBalance();
        $this->saveStakedZnn();
        $this->saveFusedQsr();
        $this->saveRewardTotals();

        $this->account->updated_at = $this->account->latestBlock?->created_at ?? now();
        $this->account->save();
    }

    public function asJob(Account $account): void
    {
        $this->handle($account);
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
            $token = Token::find($tokenId);

            $sent = $this->account->sentBlocks()
                ->selectRaw('CAST(SUM(amount) AS INTEGER) as total')
                ->where('token_id', $token->id)
                ->first()->total;

            $received = $this->account->receivedBlocks()
                ->selectRaw('CAST(SUM(amount) AS INTEGER) as total')
                ->where('token_id', $token->id)
                ->first()->total;

            $balance = $received - $sent;

            if ($token->token_standard === NetworkTokensEnum::ZNN->value) {
                $this->account->znn_balance = $balance;
            }

            if ($token->token_standard === NetworkTokensEnum::QSR->value) {
                $this->account->qsr_balance = $balance;
            }

            if ($accountTokenIds->contains($token->id)) {
                $this->account->balances()->attach($token, [
                    'balance' => $balance,
                    'updated_at' => now(),
                ]);
            } else {
                $this->account->balances()->updateExistingPivot($token->id, [
                    'balance' => $balance,
                    'updated_at' => now(),
                ]);
            }
        });
    }

    private function saveStakedZnn(): void
    {
        $this->account->znn_staked = $this->account->stakes()
            ->isActive()
            ->isZnn()
            ->sum('amount');
    }

    private function saveFusedQsr(): void
    {
        $this->account->qsr_fused = $this->account->fusions()
            ->isActive()
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
