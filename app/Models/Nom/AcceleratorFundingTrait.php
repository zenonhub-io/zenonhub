<?php

declare(strict_types=1);

namespace App\Models\Nom;

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
        $znn = app('znnToken')->getDisplayAmount($this->znn_requested);
        $qsr = app('qsrToken')->getDisplayAmount($this->qsr_requested);

        $znnTotal = ($this->znn_price * $znn);
        $qsrTotal = ($this->qsr_price * $qsr);

        return number_format(($znnTotal + $qsrTotal), 2);
    }
}
