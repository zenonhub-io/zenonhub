<?php

namespace App\Jobs\Common;

use App\Models\Nom\AccountBlock;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WithdrawQsr implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;
    public int $backoff = 10;
    public AccountBlock $block;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {

    }
}
