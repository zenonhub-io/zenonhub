<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class UpdateZnnPrice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function handle(): void
    {
        $complete = 0;
        $znnPrice = App::make('coingeko.api')->currentPrice();
        $qsrPrice = App::make('coingeko.api')->currentPrice('quasar-2');

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
