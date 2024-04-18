<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services;

use App\Domains\Nom\Models\Pillar;
use App\Domains\Nom\Models\Sentinel;
use Illuminate\Support\Collection;

class ZenonAprData
{
    public const DAYS_PER_MONTH = 30;

    public const HOURS_PER_MONTH = 24 * 30;

    public const MONTHS_PER_YEAR = 12;

    public const DAYS_PER_YEAR = 12 * 30;

    public const EPOCH_LENGTH_IN_DAYS = 1;

    public const MOMENTUMS_PER_HOUR = 360;

    public const MOMENTUMS_PER_EPOCH = 360 * 24;

    public const MOMENTUMS_PER_DAY = 8640;

    public const DAILY_ZNN_REWARDS = 4320;

    public const DAILY_QSR_REWARDS = 5000;

    public const QSR_REWARD_SHARE_FOR_STAKERS = 0.5;

    public const QSR_REWARD_SHARE_FOR_SENTINELS = 0.25;

    public const ZNN_REWARD_SHARE_FOR_SENTINELS = 0.13;

    public const QSR_REWARD_SHARE_FOR_LPS = 0.25;

    public const ZNN_REWARD_SHARE_FOR_LPS = 0.13;

    public const ZNN_REWARD_SHARE_FOR_PILLAR_DELEGATES = 0.24;

    public const ZNN_REWARD_SHARE_FOR_PILLAR_MOMENTUMS = 0.5;

    public const SENTINEL_COLLATERAL_ZNN = 5000;

    public const SENTINEL_COLLATERAL_QSR = 50000;

    public const PILLAR_COLLATERAL_ZNN = 15000;

    public const PILLAR_COLLATERAL_QSR = 150000;

    public const DECIMALS = 100000000;

    public const LP_TOKEN_DECIMALS = 1000000000000000000;

    public float $stakingApr = 0;

    public float $delegateApr = 0;

    public float $lpApr = 0;

    public float $sentinelApr = 0;

    public float $pillarTop30Apr = 0;

    public float $pillarNotTop30Apr = 0;

    protected int $yearlyZnnRewardsPoolForLp = 0;

    protected int $yearlyQsrRewardPoolForLps = 0;

    protected float $yearlyZnnRewardPoolForSentinels = 0;

    protected float $yearlyQsrRewardPoolForSentinels = 0;

    protected float $yearlyZnnMomentumRewardPoolForPillarsTop30 = 0;

    protected float $yearlyZnnMomentumRewardPoolForPillarsNotTop30 = 0;

    protected float $yearlyZnnDelegateRewardPoolForPillarDelegates = 0;

    protected float $yearlyQsrRewardPoolForStakers = 0;

    protected Collection $pillars;

    protected Collection $pillarsTop30;

    protected Collection $pillarsNotTop30;

    protected int $pillarCount = 0;

    protected int $pillarValue = 0;

    protected int $totalDelegatedZnn = 0;

    protected int $totalDelegatedZnnTop30 = 0;

    protected int $totalDelegatedZnnNotTop30 = 0;

    protected float $avgPillarMomentumRewardShareTop30 = 0;

    protected float $avgPillarDelegateRewardShareTop30 = 0;

    protected float $avgPillarMomentumRewardShareNotTop30 = 0;

    protected float $avgPillarDelegateRewardShareNotTop30 = 0;

    protected float $totalExpectedDailyMomentumsTop30 = 0;

    protected float $totalExpectedDailyMomentumsNotTop30 = 0;

    protected Collection $sentinels;

    protected int $sentinelCount = 0;

    protected int $sentinelValue = 0;

    public function __construct()
    {
        $this->setYearlyRewardPools();
        $this->setPillarData();
        $this->setSentinelData();
        $this->setStakeData();

        $this->setSentinelApr();
        $this->setPillarTop30Apr();
        $this->setPillarNotTop30Apr();
        $this->setPillarDelegateApr();
    }

    private function setPillarData(): void
    {
        $this->pillars = Pillar::isActive()->get();
        $this->pillarsTop30 = Pillar::isTop30()->isActive()->get();
        $this->pillarsNotTop30 = Pillar::isNotTop30()->isActive()->get();

        $this->pillarCount = $this->pillars->count();
        $this->pillarValue = (znn_price() * self::PILLAR_COLLATERAL_ZNN) + (qsr_price() * self::PILLAR_COLLATERAL_QSR);
        $this->totalDelegatedZnn = $this->pillars->sum('weight');
        $this->totalDelegatedZnnTop30 = $this->pillarsTop30->sum('weight');
        $this->totalDelegatedZnnNotTop30 = $this->pillarsNotTop30->sum('weight');

        $secondaryGroupSize = $this->pillarCount - 15;
        $momentumsForPrimaryGroup = self::MOMENTUMS_PER_DAY * 0.5;
        $momentumsForSecondaryGroup = self::MOMENTUMS_PER_DAY * 0.5;

        // Total expected momentums
        $this->totalExpectedDailyMomentumsTop30 = $momentumsForPrimaryGroup + $momentumsForSecondaryGroup * (15 / $secondaryGroupSize);
        $this->totalExpectedDailyMomentumsNotTop30 = $momentumsForSecondaryGroup * (($secondaryGroupSize - 15) / $secondaryGroupSize);

        // Reward share top 30
        $totalMomentumRewardShareTop30 = (int) $this->pillarsTop30->sum(fn ($pillar) => $pillar->momentum_rewards / 100);
        $totalDelegateRewardShareTop30 = (int) $this->pillarsTop30->sum(fn ($pillar) => $pillar->delegate_rewards / 100);
        $this->avgPillarMomentumRewardShareTop30 = $totalMomentumRewardShareTop30 / $this->pillarsTop30->count();
        $this->avgPillarDelegateRewardShareTop30 = $totalDelegateRewardShareTop30 / $this->pillarsTop30->count();

        // Reward share not top 30
        $totalMomentumRewardShareNotTop30 = (int) $this->pillarsNotTop30->sum(fn ($pillar) => $pillar->momentum_rewards / 100);
        $totalDelegateRewardShareNotTop30 = (int) $this->pillarsNotTop30->sum(fn ($pillar) => $pillar->delegate_rewards / 100);
        $this->avgPillarMomentumRewardShareNotTop30 = $totalMomentumRewardShareNotTop30 / $this->pillarsNotTop30->count();
        $this->avgPillarDelegateRewardShareNotTop30 = $totalDelegateRewardShareNotTop30 / $this->pillarsNotTop30->count();
    }

    private function setSentinelData(): void
    {
        $this->sentinels = Sentinel::isActive()->get();

        $this->sentinelCount = $this->sentinels->count();
        $this->sentinelValue = (znn_price() * self::SENTINEL_COLLATERAL_ZNN) + (qsr_price() * self::SENTINEL_COLLATERAL_QSR);
    }

    private function setStakeData(): void
    {
        $rewardsPerEpoch = ($this->getYearlyQsrRewards() * self::QSR_REWARD_SHARE_FOR_STAKERS) / (self::DAYS_PER_YEAR * self::EPOCH_LENGTH_IN_DAYS);

    }

    private function setYearlyRewardPools(): void
    {
        $yearlyZnnRewards = $this->getYearlyZnnRewards();
        $yearlyQsrRewards = $this->getYearlyQsrRewards();

        $this->yearlyZnnRewardPoolForSentinels = ($yearlyZnnRewards * self::ZNN_REWARD_SHARE_FOR_SENTINELS);
        $this->yearlyQsrRewardPoolForSentinels = ($yearlyQsrRewards * self::QSR_REWARD_SHARE_FOR_SENTINELS);

        $this->yearlyZnnMomentumRewardPoolForPillarsTop30 = $this->getYearlyMomentumRewardsTop30();
        $this->yearlyZnnMomentumRewardPoolForPillarsNotTop30 = $this->getYearlyMomentumRewardsNotTop30();
        $this->yearlyZnnDelegateRewardPoolForPillarDelegates = ($yearlyZnnRewards * self::ZNN_REWARD_SHARE_FOR_PILLAR_DELEGATES);

        $this->yearlyQsrRewardPoolForStakers = ($yearlyQsrRewards * self::QSR_REWARD_SHARE_FOR_STAKERS);
    }

    private function setSentinelApr(): void
    {
        $totalRewardsUsd = ($this->yearlyZnnRewardPoolForSentinels * znn_price()) + ($this->yearlyQsrRewardPoolForSentinels * qsr_price());
        $this->sentinelApr = $totalRewardsUsd / $this->sentinelCount / $this->sentinelValue * 100;
    }

    private function setPillarTop30Apr(): void
    {
        $yearlyMomentumRewards = $this->getYearlyMomentumRewardsTop30();
        $yearlyMomentumRewards *= (1 - $this->avgPillarMomentumRewardShareTop30);

        $totalYearlyDelegateRewards = $this->getYearlyZnnRewards() * self::ZNN_REWARD_SHARE_FOR_PILLAR_DELEGATES;
        $delegateRewardShare = $this->totalDelegatedZnnTop30 / $this->totalDelegatedZnn;
        $yearlyDelegateRewards = $totalYearlyDelegateRewards * $delegateRewardShare;
        $yearlyDelegateRewards *= (1 - $this->avgPillarMomentumRewardShareTop30);

        $pillarsTop30Count = $this->pillarsTop30->count();
        $totalRewardsUsd = ($yearlyMomentumRewards * znn_price()) + ($yearlyDelegateRewards * znn_price());
        $this->pillarTop30Apr = $totalRewardsUsd / $pillarsTop30Count / $this->pillarValue * 100;
    }

    private function setPillarNotTop30Apr(): void
    {
        $yearlyMomentumRewards = $this->getYearlyMomentumRewardsNotTop30();
        $yearlyMomentumRewards *= (1 - $this->avgPillarMomentumRewardShareNotTop30);

        $totalYearlyDelegateRewards = $this->getYearlyZnnRewards() * self::ZNN_REWARD_SHARE_FOR_PILLAR_DELEGATES;
        $delegateRewardShare = $this->totalDelegatedZnnNotTop30 / $this->totalDelegatedZnn;
        $yearlyDelegateRewards = $totalYearlyDelegateRewards * $delegateRewardShare;
        $yearlyDelegateRewards *= (1 - $this->avgPillarMomentumRewardShareNotTop30);

        $pillarsNotTop30Count = $this->pillarsNotTop30->count();
        $totalRewardsUsd = ($yearlyMomentumRewards * znn_price()) + ($yearlyDelegateRewards * znn_price());
        $this->pillarNotTop30Apr = $totalRewardsUsd / $pillarsNotTop30Count / $this->pillarValue * 100;
    }

    private function setPillarDelegateApr(): void
    {

    }

    private function getYearlyZnnRewards(): int
    {
        return self::DAILY_ZNN_REWARDS * (self::DAYS_PER_MONTH * self::MONTHS_PER_YEAR);
    }

    private function getYearlyQsrRewards(): int
    {
        return self::DAILY_QSR_REWARDS * (self::DAYS_PER_MONTH * self::MONTHS_PER_YEAR);
    }

    private function getYearlyMomentumRewardsTop30(): float
    {
        $totalYearlyMomentumRewards = $this->getYearlyZnnRewards() * self::ZNN_REWARD_SHARE_FOR_PILLAR_MOMENTUMS;
        $momentumRewardShare = $this->totalExpectedDailyMomentumsTop30 / self::MOMENTUMS_PER_DAY;

        return $totalYearlyMomentumRewards * $momentumRewardShare;
    }

    private function getYearlyMomentumRewardsNotTop30(): float
    {
        $totalYearlyMomentumRewards = $this->getYearlyZnnRewards() * self::ZNN_REWARD_SHARE_FOR_PILLAR_MOMENTUMS;
        $momentumRewardShare = $this->totalExpectedDailyMomentumsNotTop30 / self::MOMENTUMS_PER_DAY;

        return $totalYearlyMomentumRewards * $momentumRewardShare;
    }
}
