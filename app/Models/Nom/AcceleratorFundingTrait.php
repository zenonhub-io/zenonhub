<?php

declare(strict_types=1);

namespace App\Models\Nom;

use App\Services\CoinGecko;

trait AcceleratorFundingTrait
{
    public function getDisplayZnnRequestedAttribute(): string
    {
        return app('znnToken')->getFormattedAmount($this->znn_requested);
    }

    public function getDisplayQsrRequestedAttribute(): string
    {
        return app('qsrToken')->getFormattedAmount($this->qsr_requested);
    }

    public function getDisplayUsdRequestedAttribute(): string
    {
        if (! $this->znn_price || ! $this->qsr_price) {
            $znnPrice = app(CoinGecko::class)->historicPrice('zenon-2', 'usd', $this->created_at);
            $qsrPrice = app(CoinGecko::class)->historicPrice('quasar', 'usd', $this->created_at);

            // Projects created before QSR price available
            if (is_null($qsrPrice) && $znnPrice > 0) {
                $qsrPrice = $znnPrice / 10;
            }

            if ($znnPrice > 0) {
                $this->znn_price = $znnPrice;
                $this->saveQuietly();
            }

            if ($qsrPrice > 0) {
                $this->qsr_price = $qsrPrice;
                $this->saveQuietly();
            }
        }

        $znn = app('znnToken')->getDisplayAmount($this->znn_requested);
        $qsr = app('qsrToken')->getDisplayAmount($this->qsr_requested);

        $znnTotal = ($this->znn_price * $znn);
        $qsrTotal = ($this->qsr_price * $qsr);

        return number_format(($znnTotal + $qsrTotal), 2);
    }
}
