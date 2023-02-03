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

    public int $tries = 5;

    public function handle(): void
    {
        $price = App::make('coingeko.api')->currenPrice();

        if ($price) {
            Cache::forever("znn-price", $price);
        } else {
            $this->release(30);
        }
    }
}
