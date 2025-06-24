<?php

declare(strict_types=1);

namespace App\Services\AprData;

use App\Models\Nom\Pillar;
use Illuminate\Support\Collection;

class PillarsApr extends BaseApr
{
    private const array PILLAR_REWARD_SHARE = [
        'DELEGATES' => 0.24,
        'MOMENTUMS' => 0.5,
    ];

    private const array PILLAR_COLLATERAL = [
        'ZNN' => 15000,
        'QSR' => 150000,
    ];

    public float $pillarTop30Apr = 0 {
        get {
            return $this->pillarTop30Apr;
        }
    }

    public float $pillarNotTop30Apr = 0 {
        get {
            return $this->pillarNotTop30Apr;
        }
    }

    public float $delegateApr = 0 {
        get {
            return $this->delegateApr;
        }
    }

    private Collection $pillars;

    private Collection $pillarsTop30;

    private Collection $pillarsNotTop30;

    private float $yearlyZnnMomentumRewardPoolForPillarsTop30 = 0;

    private float $yearlyZnnMomentumRewardPoolForPillarsNotTop30 = 0;

    private float $yearlyZnnDelegateRewardPoolForPillarDelegates = 0;

    private float $pillarValue = 0;

    private int $totalDelegatedZnn = 0;

    private int $totalDelegatedZnnTop30 = 0;

    private int $totalDelegatedZnnNotTop30 = 0;

    private float $avgPillarMomentumRewardShareTop30 = 0;

    private float $avgPillarDelegateRewardShareTop30 = 0;

    private float $avgPillarMomentumRewardShareNotTop30 = 0;

    private float $avgPillarDelegateRewardShareNotTop30 = 0;

    private float $totalExpectedDailyMomentumsTop30 = 0;

    private float $totalExpectedDailyMomentumsNotTop30 = 0;

    public function __construct()
    {
        parent::__construct();

        $this->setPillarData();
        $this->setYearlyRewardPools();
        $this->updatePillars();
        $this->setPillarTop30Apr();
        $this->setPillarNotTop30Apr();
        $this->setDelegateApr();
    }

    private function setPillarData(): void
    {
        $this->pillars = Pillar::whereActive()->get();
        $this->pillarsTop30 = Pillar::whereTop30()->whereActive()->get();
        $this->pillarsNotTop30 = Pillar::whereNotTop30()->whereActive()->get();

        $this->pillarValue = ($this->znnPrice * self::PILLAR_COLLATERAL['ZNN']) + ($this->qsrPrice * self::PILLAR_COLLATERAL['QSR']);
        $this->totalDelegatedZnn = $this->pillars->sum('weight');
        $this->totalDelegatedZnnTop30 = $this->pillarsTop30->sum('weight');
        $this->totalDelegatedZnnNotTop30 = $this->pillarsNotTop30->sum('weight');

        $secondaryGroupSize = $this->pillars->count() - 15;
        $momentumsForPrimaryGroup = self::MOMENTUMS_PER_DAY * 0.5;
        $momentumsForSecondaryGroup = self::MOMENTUMS_PER_DAY * 0.5;

        // Total expected momentums
        $this->totalExpectedDailyMomentumsTop30 = $momentumsForPrimaryGroup + $momentumsForSecondaryGroup * (15 / $secondaryGroupSize);
        $this->totalExpectedDailyMomentumsNotTop30 = $momentumsForSecondaryGroup * (($secondaryGroupSize - 15) / $secondaryGroupSize);

        // Reward share top 30
        $this->avgPillarMomentumRewardShareTop30 = $this->pillarsTop30->avg('momentum_rewards') / 100;
        $this->avgPillarDelegateRewardShareTop30 = $this->pillarsTop30->avg('delegate_rewards') / 100;

        // Reward share not top 30
        $this->avgPillarMomentumRewardShareNotTop30 = $this->pillarsNotTop30->avg('momentum_rewards') / 100;
        $this->avgPillarDelegateRewardShareNotTop30 = $this->pillarsNotTop30->avg('delegate_rewards') / 100;
    }

    private function setYearlyRewardPools(): void
    {
        $this->yearlyZnnMomentumRewardPoolForPillarsTop30 = $this->getYearlyMomentumRewardsTop30();
        $this->yearlyZnnMomentumRewardPoolForPillarsNotTop30 = $this->getYearlyMomentumRewardsNotTop30();
        $this->yearlyZnnDelegateRewardPoolForPillarDelegates = ($this->yearlyZnnRewards * self::PILLAR_REWARD_SHARE['DELEGATES']);
    }

    private function updatePillars(): void
    {
        $top30Count = $this->pillarsTop30->count();
        $notTop30Count = $this->pillarsNotTop30->count();

        $this->pillars->each(function (Pillar $pillar) use ($top30Count, $notTop30Count) {

            $rewardMultiplier = 1;
            $yearlyMomentumRewards = 0;

            if ($pillar->expected_momentums - $pillar->produced_momentums > 2 && $pillar->expected_momentums > 0) {
                $rewardMultiplier = $pillar->produced_momentums / $pillar->expected_momentums;
            }

            if ($pillar->rank < 30 && $top30Count > 0) {
                $dailyExpectedMomentumsPerPillar = $this->totalExpectedDailyMomentumsTop30 / $top30Count;
                $yearlyMomentumRewards = $this->yearlyZnnMomentumRewardPoolForPillarsTop30 * ($dailyExpectedMomentumsPerPillar * $rewardMultiplier) / $this->totalExpectedDailyMomentumsTop30;
            } elseif ($notTop30Count > 0) {
                $dailyExpectedMomentumsPerPillar = $this->totalExpectedDailyMomentumsNotTop30 / $notTop30Count;
                $yearlyMomentumRewards = $this->yearlyZnnMomentumRewardPoolForPillarsNotTop30 * ($dailyExpectedMomentumsPerPillar * $rewardMultiplier) / $this->totalExpectedDailyMomentumsNotTop30;
            }

            $yearlyDelegateRewards = 0;
            if ($this->totalDelegatedZnn > 0) {
                $yearlyDelegateRewards = ($pillar->weight / $this->totalDelegatedZnn) * $this->yearlyZnnDelegateRewardPoolForPillarDelegates;
                $yearlyDelegateRewards *= $rewardMultiplier;
            }

            $pillar->owner_apr = $this->getPillarApr($pillar, $yearlyMomentumRewards, $yearlyDelegateRewards);
            $pillar->delegate_apr = $this->getPillarDelegateApr($pillar, $yearlyMomentumRewards, $yearlyDelegateRewards);
            $pillar->save();
        });
    }

    private function setPillarTop30Apr(): void
    {
        $yearlyMomentumRewards = $this->yearlyZnnMomentumRewardPoolForPillarsTop30;
        $yearlyMomentumRewards *= (1 - $this->avgPillarMomentumRewardShareTop30);

        $totalYearlyDelegateRewards = $this->yearlyZnnRewards * self::PILLAR_REWARD_SHARE['DELEGATES'];
        $delegateRewardShare = $this->totalDelegatedZnnTop30 / $this->totalDelegatedZnn;
        $yearlyDelegateRewards = $totalYearlyDelegateRewards * $delegateRewardShare;
        $yearlyDelegateRewards *= (1 - $this->avgPillarDelegateRewardShareTop30);

        $pillarsTop30Count = $this->pillarsTop30->count();
        $totalRewardsUsd = ($yearlyMomentumRewards * $this->znnPrice) + ($yearlyDelegateRewards * $this->znnPrice);
        $this->pillarTop30Apr = $totalRewardsUsd / $pillarsTop30Count / $this->pillarValue * 100;
    }

    private function setPillarNotTop30Apr(): void
    {
        $yearlyMomentumRewards = $this->yearlyZnnMomentumRewardPoolForPillarsNotTop30;
        $yearlyMomentumRewards *= (1 - $this->avgPillarMomentumRewardShareNotTop30);

        $totalYearlyDelegateRewards = $this->yearlyZnnRewards * self::PILLAR_REWARD_SHARE['DELEGATES'];
        $delegateRewardShare = $this->totalDelegatedZnnNotTop30 / $this->totalDelegatedZnn;
        $yearlyDelegateRewards = $totalYearlyDelegateRewards * $delegateRewardShare;
        $yearlyDelegateRewards *= (1 - $this->avgPillarDelegateRewardShareNotTop30);

        $pillarsNotTop30Count = $this->pillarsNotTop30->count();
        $totalRewardsUsd = ($yearlyMomentumRewards * $this->znnPrice) + ($yearlyDelegateRewards * $this->znnPrice);
        $this->pillarNotTop30Apr = $totalRewardsUsd / $pillarsNotTop30Count / $this->pillarValue * 100;
    }

    private function setDelegateApr(): void
    {
        $this->delegateApr = $this->pillars
            ->where('delegate_apr', '>', '0')
            ->avg('delegate_apr');
    }

    private function getPillarApr(Pillar $pillar, float $yearlyMomentumRewards, float $yearlyDelegateRewards): float
    {
        $momentumRewardShare = $pillar->momentum_rewards / 100;
        $delegateRewardShare = $pillar->delegate_rewards / 100;

        $momentumRewardsValue = ($yearlyMomentumRewards * (1 - $momentumRewardShare) * $this->znnPrice);
        $delegateRewardsValue = ($yearlyDelegateRewards * (1 - $delegateRewardShare) * $this->znnPrice);
        $rewardsValue = $momentumRewardsValue + $delegateRewardsValue;

        return $this->pillarValue > 0 ? ($rewardsValue / $this->pillarValue) * 100 : 0;
    }

    private function getPillarDelegateApr(Pillar $pillar, float $yearlyMomentumRewards, float $yearlyDelegateRewards): float
    {
        $momentumRewardShare = $pillar->momentum_rewards / 100;
        $delegateRewardShare = $pillar->delegate_rewards / 100;

        $momentumRewardsValue = $yearlyMomentumRewards * $momentumRewardShare;
        $delegateRewardsValue = $yearlyDelegateRewards * $delegateRewardShare;
        $rewardsValueZnn = $momentumRewardsValue + $delegateRewardsValue;
        $rewardsValueZnn *= self::DECIMALS;

        return $pillar->weight > 0 ? ($rewardsValueZnn / $pillar->weight) * 100 : 0;
    }

    private function getYearlyMomentumRewardsTop30(): float
    {
        $totalYearlyMomentumRewards = $this->yearlyZnnRewards * self::PILLAR_REWARD_SHARE['MOMENTUMS'];
        $momentumRewardShare = $this->totalExpectedDailyMomentumsTop30 / self::MOMENTUMS_PER_DAY;

        return $totalYearlyMomentumRewards * $momentumRewardShare;
    }

    private function getYearlyMomentumRewardsNotTop30(): float
    {
        $totalYearlyMomentumRewards = $this->yearlyZnnRewards * self::PILLAR_REWARD_SHARE['MOMENTUMS'];
        $momentumRewardShare = $this->totalExpectedDailyMomentumsNotTop30 / self::MOMENTUMS_PER_DAY;

        return $totalYearlyMomentumRewards * $momentumRewardShare;
    }
}
