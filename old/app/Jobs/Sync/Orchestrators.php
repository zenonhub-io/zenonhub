<?php

namespace App\Jobs\Sync;

use App\Actions\SyncOrchestrators;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class Orchestrators implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    protected Collection $pillars;

    public function handle()
    {
        try {
            (new SyncOrchestrators())->execute();
        } catch (Throwable $exception) {
            Log::warning('Sync orchestrators error');
            Log::debug($exception);
            $this->release(30);
        }
    }
}
