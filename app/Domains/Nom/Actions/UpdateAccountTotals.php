<?php

declare(strict_types=1);

namespace App\Domains\Nom\Actions;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Token;

class UpdateAccountTotals
{
    private Account $account;

    public function execute(Account $account): void
    {
        if ($account->address === config('explorer.empty_address')) {
            return;
        }

        $this->account = $account;

        $this->saveCurrentBalance();
        $this->updateSendReceiveTotals();
        $this->saveStakedZnn();
        $this->saveFusedQsr();
        $this->saveRewardTotals();

        $this->account->updated_at = $this->account->latestBlock?->created_at ?? now();
        $this->account->save();
    }

    private function saveCurrentBalance(): void
    {
        $tokenIds = AccountBlock::involvingAccount($this->account)
            ->select('token_id')
            ->whereNotNull('token_id')
            ->groupBy('token_id')
            ->pluck('token_id');

        $accountTokenIds = $this->account->balances()
            ->pluck('token_id')
            ->toArray();

        $tokenIds->each(function ($tokenId) use ($accountTokenIds) {
            $token = Token::find($tokenId);
            $totalSent = $this->account->sentBlocks()->where('token_id', $tokenId)->sum('amount');
            $totalReceived = $this->account->receivedBlocks()->where('token_id', $tokenId)->sum('amount');
            $balance = $totalReceived - $totalSent;

            if (! in_array($token->id, $accountTokenIds, true)) {
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

    private function updateSendReceiveTotals(): void
    {
        $znnTokenId = app('znnToken')->id;
        $qsrTokenId = app('qsrToken')->id;

        $this->account->znn_sent = $this->account->sentBlocks()->where('token_id', $znnTokenId)->sum('amount');
        $this->account->znn_received = $this->account->receivedBlocks()->where('token_id', $znnTokenId)->sum('amount');

        $this->account->qsr_sent = $this->account->sentBlocks()->where('token_id', $qsrTokenId)->sum('amount');
        $this->account->qsr_received = $this->account->receivedBlocks()->where('token_id', $qsrTokenId)->sum('amount');
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
