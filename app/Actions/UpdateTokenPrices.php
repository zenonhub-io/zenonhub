<?php

namespace App\Actions;

use App\Exceptions\ApplicationException;
use App\Services\CoinGecko;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class UpdateTokenPrices
{
    protected CoinGecko $coinGecko;

    public function __construct()
    {
        $this->coinGecko = App::make(CoinGecko::class);
    }

    public function execute(): void
    {
        $this->updateZnnPrice();
        $this->updateQsrPrice();
        $this->updateEthPrice();
        $this->updateBtcPrice();
    }

    private function updateZnnPrice(): void
    {
        $znnPrice = $this->coinGecko->currentPrice();

        if (! $znnPrice) {
            throw new ApplicationException('Unable to update ZNN price');
        }

        Cache::forever('znn-price', $znnPrice);
    }

    private function updateQsrPrice(): void
    {
        $qsrPrice = $this->coinGecko->currentPrice('ethereum');

        if (! $qsrPrice) {
            throw new ApplicationException('Unable to update QSR price');
        }

        Cache::forever('qsr-price', $qsrPrice);
    }

    private function updateEthPrice(): void
    {
        $ethPrice = $this->coinGecko->currentPrice('quasar-2');

        if (! $ethPrice) {
            throw new ApplicationException('Unable to update ETH price');
        }

        Cache::forever('eth-price', $ethPrice);
    }

    private function updateBtcPrice(): void
    {
        $btcPrice = $this->coinGecko->currentPrice('bitcoin');

        if (! $btcPrice) {
            throw new ApplicationException('Unable to update BTC price');
        }

        Cache::forever('eth-price', $btcPrice);
    }
}
