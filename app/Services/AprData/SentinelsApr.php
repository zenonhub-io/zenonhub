<?php

declare(strict_types=1);

namespace App\Services\AprData;

use App\Models\Nom\Sentinel;

class SentinelsApr extends BaseApr
{
    private const array SENTINEL_REWARD_SHARE = [
        'ZNN' => 0.13,
        'QSR' => 0.25,
    ];

    private const array SENTINEL_COLLATERAL = [
        'ZNN' => 5000,
        'QSR' => 50000,
    ];

    public float $sentinelApr = 0 {
        get {
            return $this->sentinelApr;
        }
    }

    private float $yearlyZnnRewardPoolForSentinels = 0;

    private float $yearlyQsrRewardPoolForSentinels = 0;

    private int $sentinelCount = 0;

    private float $sentinelValue = 0;

    public function __construct()
    {
        parent::__construct();

        $this->setSentinelData();
        $this->setYearlyRewardPools();
        $this->setSentinelApr();
    }

    private function setSentinelData(): void
    {
        $sentinels = Sentinel::whereActive()->get();
        $this->sentinelCount = $sentinels->count();
        $this->sentinelValue = ($this->znnPrice * self::SENTINEL_COLLATERAL['ZNN']) + ($this->qsrPrice * self::SENTINEL_COLLATERAL['QSR']);
    }

    private function setYearlyRewardPools(): void
    {
        $this->yearlyZnnRewardPoolForSentinels = ($this->yearlyZnnRewards * self::SENTINEL_REWARD_SHARE['ZNN']);
        $this->yearlyQsrRewardPoolForSentinels = ($this->yearlyQsrRewards * self::SENTINEL_REWARD_SHARE['QSR']);
    }

    private function setSentinelApr(): void
    {
        if (! $this->sentinelCount || ! $this->sentinelValue) {
            $this->sentinelApr = 0;

            return;
        }

        $znnRewardsUsd = $this->yearlyZnnRewardPoolForSentinels * $this->znnPrice;
        $qsrRewardsUsd = $this->yearlyQsrRewardPoolForSentinels * $this->qsrPrice;
        $totalRewardsUsd = $znnRewardsUsd + $qsrRewardsUsd;

        $this->sentinelApr = $totalRewardsUsd / $this->sentinelCount / $this->sentinelValue * 100;
    }
}
