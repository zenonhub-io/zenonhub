<?php

declare(strict_types=1);

namespace App\Services\AprData;

class BaseApr
{
    protected const DAYS_PER_MONTH = 30;

    protected const HOURS_PER_MONTH = 24 * 30;

    protected const MONTHS_PER_YEAR = 12;

    protected const DAYS_PER_YEAR = 12 * 30;

    protected const EPOCH_LENGTH_IN_DAYS = 1;

    protected const MOMENTUMS_PER_HOUR = 360;

    protected const MOMENTUMS_PER_EPOCH = 360 * 24;

    protected const MOMENTUMS_PER_DAY = 8640;

    protected const DAILY_ZNN_REWARDS = 4320;

    protected const DAILY_QSR_REWARDS = 5000;

    protected const DECIMALS = 100000000;

    protected int $yearlyZnnRewards;

    protected int $yearlyQsrRewards;

    protected mixed $znnPrice;

    protected mixed $qsrPrice;

    public function __construct()
    {
        $this->setYearlyRewards();
        $this->setTokenPrice();
    }

    protected function setYearlyRewards(): void
    {
        $this->yearlyZnnRewards = self::DAILY_ZNN_REWARDS * (self::DAYS_PER_MONTH * self::MONTHS_PER_YEAR);
        $this->yearlyQsrRewards = self::DAILY_QSR_REWARDS * (self::DAYS_PER_MONTH * self::MONTHS_PER_YEAR);
    }

    protected function setTokenPrice(): void
    {
        $this->znnPrice = app('znnToken')->price;
        $this->qsrPrice = $this->znnPrice / 10;
        // $this->qsrPrice = app('qsrToken')->price;
    }
}
