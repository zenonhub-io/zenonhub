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

    public int $tries = 2;

    public function handle(): void
    {
        $complete = 0;
        $znnPrice = App::make('coingeko.api')->currenPrice();
        $qsrPrice = App::make('coingeko.api')->currenPrice('quasar');

        if ($znnPrice > 0) {
            Cache::forever('znn-price', $znnPrice);
            $complete++;
        }

        if ($qsrPrice > 0) {
            Cache::forever('qsr-price', $qsrPrice);
            $complete++;
        }

        if ($complete === 2) {
            $this->delete();
        } else {
            $this->release(20);
        }
    }
}
