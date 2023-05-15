<?php

namespace App\Jobs;

use App;
use Cache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateZnnPrice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function handle(): void
    {
        $znnPrice = App::make('coingeko.api')->currenPrice();
        $qsrPrice = App::make('coingeko.api')->currenPrice('quasar');

        if ($znnPrice) {
            Cache::forever('znn-price', $znnPrice);
        }

        if ($qsrPrice) {
            Cache::forever('qsr-price', $qsrPrice);
        }

        $this->release(30);
    }
}
