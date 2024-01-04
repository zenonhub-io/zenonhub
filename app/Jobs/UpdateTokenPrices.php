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
use Illuminate\Support\Sleep;

class UpdateTokenPrices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function handle(): void
    {
        $complete = 0;
        $coinGecko = App::make(CoinGecko::class);

        $znnPrice = $coinGecko->currentPrice();
        if ($znnPrice > 0) {
            Cache::forever('znn-price', $znnPrice);
            $complete++;
        }

        Sleep::for(1)->second();
        $qsrPrice = $coinGecko->currentPrice('quasar-2');

        if ($qsrPrice > 0) {
            Cache::forever('qsr-price', $qsrPrice);
            $complete++;
        }

        //        Sleep::for(1)->second();
        //        $ethPrice = $coinGecko->currentPrice('ethereum');
        //
        //        if ($ethPrice > 0) {
        //            Cache::forever('eth-price', $ethPrice);
        //            $complete++;
        //        }
        //
        //        Sleep::for(1)->second();
        //        $btcPrice = $coinGecko->currentPrice('bitcoin');
        //
        //        if ($btcPrice > 0) {
        //            Cache::forever('btc-price', $btcPrice);
        //            $complete++;
        //        }

        if ($complete === 2) {
            $this->delete();
        } else {
            $this->release(20);
        }
    }
}
