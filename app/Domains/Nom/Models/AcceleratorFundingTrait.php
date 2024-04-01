<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use App\Services\CoinGecko;

trait AcceleratorFundingTrait
{
    public function getDisplayZnnRequestedAttribute(): string
    {
        return znn_token()->getFormattedAmount($this->znn_requested);
    }

    public function getDisplayQsrRequestedAttribute(): string
    {
        return qsr_token()->getFormattedAmount($this->qsr_requested);
    }

    public function getDisplayUsdRequestedAttribute(): string
    {
        if (! $this->znn_price || ! $this->qsr_price) {
            $znnPrice = App::make(CoinGecko::class)->historicPrice('zenon-2', 'usd', $this->created_at->timestamp);
            $qsrPrice = App::make(CoinGecko::class)->historicPrice('quasar', 'usd', $this->created_at->timestamp);

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

        $znn = znn_token()->getDisplayAmount($this->znn_requested);
        $qsr = qsr_token()->getDisplayAmount($this->qsr_requested);

        $znnTotal = ($this->znn_price * $znn);
        $qsrTotal = ($this->qsr_price * $qsr);

        return number_format(($znnTotal + $qsrTotal), 2);
    }
}
