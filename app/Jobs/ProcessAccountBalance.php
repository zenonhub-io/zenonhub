<?php

namespace App\Jobs;

use App\Models\Nom\Account;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAccountBalance implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [30, 30, 30, 60, 120];
    }

    public function __construct(
        protected Account $account
    ) {
    }

    public function handle(): void
    {
        try {
            (new \App\Actions\ProcessAccountBalance($this->account))->execute();
        } catch (\Exception $exception) {
            Log::warning('Sync balances error');
            Log::debug($exception);
            $this->release(30);
        }
    }
}
