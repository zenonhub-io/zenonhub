<?php

namespace App\Jobs;

use App\Services\CoinGecko;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class UpdateTokenPrices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function handle(): void
    {
        $complete = 0;
        $coinGecko = App::make(CoinGecko::class);
        $znnPrice = $coinGecko->currentPrice();
        $qsrPrice = $coinGecko->currentPrice('quasar-2');
        $ethPrice = $coinGecko->currentPrice('ethereum');
        $btcPrice = $coinGecko->currentPrice('bitcoin');

        if ($znnPrice > 0) {
            Cache::forever('znn-price', $znnPrice);
            $complete++;
        }

        if ($qsrPrice > 0) {
            Cache::forever('qsr-price', $qsrPrice);
            $complete++;
        }

        if ($ethPrice > 0) {
            Cache::forever('eth-price', $ethPrice);
            $complete++;
        }

        if ($btcPrice > 0) {
            Cache::forever('btc-price', $btcPrice);
            $complete++;
        }

        if ($complete === 4) {
            $this->delete();
        } else {
            $this->release(20);
        }
    }
}
