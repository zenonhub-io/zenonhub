<?php

declare(strict_types=1);

namespace App\Jobs\Nom\Plasma;

use App\Actions\SetBlockAsProcessed;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Plasma;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

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
        $fusion = Plasma::whereHash($blockData['id'])->first();

        if ($fusion) {
            $fusion->ended_at = $this->block->created_at;
            $fusion->save();
        }

        $fusedQsr = app('qsrToken')->getFormattedAmount(Plasma::isActive()->sum('amount'), 0);
        Cache::put('fused-qsr', $fusedQsr);

        (new SetBlockAsProcessed($this->block))->execute();

        \App\Events\Nom\Plasma\CancelFuse::dispatch($this->block, $blockData);
    }
}
