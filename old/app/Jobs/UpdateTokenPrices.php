<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateTokenPrices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function handle(): void
    {
        try {
            (new \App\Actions\UpdateTokenPrices())->execute();
            $this->delete();
        } catch (\Throwable $exception) {
            $this->release(20);
        }
    }
}
