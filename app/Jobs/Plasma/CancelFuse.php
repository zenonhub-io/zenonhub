<?php

namespace App\Jobs\Plasma;

use Cache;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Fusion;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CancelFuse implements ShouldQueue
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
        $blockData = $this->block->data->decoded;
        $fusion = Fusion::whereHash($blockData['id'])->first();

        if ($fusion) {
            $fusion->ended_at = $this->block->created_at;
            $fusion->save();
        }

        $fusedQsr = qsr_token()->getDisplayAmount(Fusion::isActive()->sum('amount'), 0);
        Cache::put('fused-qsr', $fusedQsr);
    }
}
