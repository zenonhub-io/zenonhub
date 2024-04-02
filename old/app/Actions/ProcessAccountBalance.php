<?php

declare(strict_types=1);

namespace App\Actions;

use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\Token;

class ProcessAccountBalance
{
    public function __construct(
        protected Account $account
    ) {
    }

    public function execute(): void
    {
        $this->saveCurrentBalance();
        $this->saveStakedZnn();
        $this->saveFusedQsr();
        $this->saveLockedTotals();
        $this->saveTotals();
        $this->saveRewardTotals();

        $this->account->updated_at = $this->account->latestBlock?->created_at ?? null;
        $this->account->save();
    }

    private function saveCurrentBalance(): void
    {
        $apiData = $this->account->raw_json;

        foreach ($apiData->balanceInfoMap as $tokenStandard => $holdings) {
            if ($tokenStandard === NetworkTokensEnum::ZNN->value) {
                $this->account->znn_balance = $holdings->balance;
            }

            if ($tokenStandard === NetworkTokensEnum::QSR->value) {
                $this->account->qsr_balance = $holdings->balance;
            }

            $token = Token::findBy('token_standard', $tokenStandard);
            $tokenIds = $this->account->balances()
                ->pluck('token_id')
                ->toArray();

            if (! in_array($token->id, $tokenIds)) {
                $this->account->balances()->attach($token, [
                    'balance' => $holdings->balance,
                    'updated_at' => now(),
                ]);
            } else {
                $this->account->balances()->updateExistingPivot($token->id, [
                    'balance' => $holdings->balance,
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function saveStakedZnn(): void
    {
        $this->account->znn_staked = $this->account->stakes()
            ->where('token_id', znn_token()->id)
            ->whereNull('ended_at')
            ->sum('amount');
    }

    private function saveFusedQsr(): void
    {
        $this->account->qsr_fused = $this->account->fusions()
            ->whereNull('ended_at')
            ->sum('amount');
    }

    private function saveLockedTotals(): void
    {
        $lockedZnn = 0;
        $lockedQsr = 0;

        $this->account
            ->pillars()
            ->whereNull('revoked_at')
            ->each(function ($pillar) use (&$lockedZnn, &$lockedQsr) {
                $lockedZnn += 1500000000000;
            });

        $this->account
            ->sentinels()
            ->whereNull('revoked_at')
            ->each(function ($sentinel) use (&$lockedZnn, &$lockedQsr) {
                $lockedZnn += 500000000000;
                $lockedQsr += 5000000000000;
            });

        $this->account->znn_locked = $lockedZnn;
        $this->account->qsr_locked = $lockedQsr;
    }

    private function saveTotals(): void
    {
        $this->account->total_znn_balance = ($this->account->znn_balance + $this->account->znn_staked + $this->account->znn_locked);
        $this->account->total_qsr_balance = ($this->account->qsr_balance + $this->account->qsr_fused + $this->account->qsr_locked);
    }

    private function saveRewardTotals(): void
    {
        $this->account->total_znn_rewards = $this->account
            ->rewards()
            ->where('token_id', znn_token()->id)
            ->sum('amount');

        $this->account->total_qsr_rewards = $this->account
            ->rewards()
            ->where('token_id', qsr_token()->id)
            ->sum('amount');
    }
}
