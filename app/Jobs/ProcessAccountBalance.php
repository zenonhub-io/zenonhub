<?php

namespace App\Jobs;

use App;
use Log;
use App\Models\Nom\Account;
use App\Models\Nom\Token;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAccountBalance implements ShouldBeUnique, ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;
    public Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account->refresh();
        $this->onQueue('indexer');
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->account->id;
    }

    public function handle(): void
    {
        try {
            $this->saveCurrentBalance();
            $this->saveStakedZnn();
            $this->saveFusedQsr();
            $this->saveLockedTotals();
            $this->saveTotals();
            $this->saveRewardTotals();

            $this->account->updated_at = now();
            $this->account->save();
        } catch (\DigitalSloth\ZnnPhp\Exceptions\Exception) {
            Log::error('Sync balances error - could not load data from API');
            $this->release(10);
        } catch (\Exception $exception) {
            Log::error('Sync balances error');
            $this->release(10);
        }
    }

    private function saveCurrentBalance()
    {
        $znn = App::make('zenon.api');
        $apiData = $znn->ledger->getAccountInfoByAddress($this->account->address);

        foreach ($apiData['data']->balanceInfoMap as $tokenStandard => $holdings) {

            if ($tokenStandard === Token::ZTS_ZNN) {
                $this->account->znn_balance = $holdings->balance;
            }

            if ($tokenStandard === Token::ZTS_QSR) {
                $this->account->qsr_balance = $holdings->balance;
            }

            $token = Token::whereZts($tokenStandard)->first();
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

    private function saveStakedZnn()
    {
        $this->account->znn_staked = $this->account->stakes()
            ->whereNull('ended_at')
            ->sum('amount');
    }

    private function saveFusedQsr()
    {
        $this->account->qsr_fused = $this->account->fusions()
            ->whereNull('ended_at')
            ->sum('amount');
    }

    private function saveLockedTotals()
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

    private function saveTotals()
    {
        $this->account->total_znn_balance = ($this->account->znn_balance + $this->account->znn_staked + $this->account->znn_locked);
        $this->account->total_qsr_balance = ($this->account->qsr_balance + $this->account->qsr_fused + $this->account->qsr_locked);
    }

    private function saveRewardTotals()
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
