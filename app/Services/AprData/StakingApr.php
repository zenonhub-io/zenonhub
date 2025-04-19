<?php

declare(strict_types=1);

namespace App\Services\AprData;

use App\Enums\Nom\EmbeddedContractsEnum;
use App\Models\Nom\Account;
use App\Services\ZenonSdk\ZenonSdk;

class StakingApr extends BaseApr
{
    private const array STAKING_REWARD_SHARE = [
        'QSR' => 0.5,
    ];

    public float $stakingApr = 0;

    protected float $yearlyQsrRewardPoolForStakers = 0;

    protected string $totalStakedZnn;

    public function __construct()
    {
        parent::__construct();

        $this->setStakeData();
        $this->setYearlyRewardPools();
        $this->setStakingApr();
    }

    private function setStakeData(): void
    {
        $rewardsPerEpoch = ($this->yearlyQsrRewards * self::STAKING_REWARD_SHARE['QSR']) / (self::DAYS_PER_YEAR * self::EPOCH_LENGTH_IN_DAYS);
        $this->totalStakedZnn = Account::firstWhere('address', EmbeddedContractsEnum::STAKE->value)->znn_balance;

        $stakingRewardsPreviousEpoch = app(ZenonSdk::class)->getStakeFrontierRewardByPage('z1qra2x3g36a8pyxw950zw90l6ac0jk3dwd8c4sk', 0, 1)[0]->qsrAmount;
        $stakingAmount = app(ZenonSdk::class)->getStakeEntriesByAddress('z1qra2x3g36a8pyxw950zw90l6ac0jk3dwd8c4sk', 0, 1)[0]->weightedAmount;

        dd($stakingAmount);
    }

    private function setYearlyRewardPools(): void
    {
        $this->yearlyQsrRewardPoolForStakers = ($this->yearlyQsrRewards * self::STAKING_REWARD_SHARE['QSR']);
    }

    private function setStakingApr(): void
    {
        $totalRewardsUsd = ($this->yearlyQsrRewardPoolForStakers * app('znnToken')->price) + ($this->yearlyQsrRewardPoolForSentinels * app('qsrToken')->price);
        $this->stakingApr = $totalRewardsUsd / $this->sentinelCount / $this->sentinelValue * 100;
    }
}
