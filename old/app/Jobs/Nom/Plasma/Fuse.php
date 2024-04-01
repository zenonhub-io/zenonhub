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

class Fuse implements ShouldQueue
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
        $toAccount = load_account($blockData['address']);

        Plasma::create([
            'chain_id' => $this->block->chain->id,
            'from_account_id' => $this->block->account_id,
            'to_account_id' => $toAccount->id,
            'amount' => $this->block->amount,
            'hash' => $this->block->hash,
            'started_at' => $this->block->created_at,
            'ended_at' => null,
        ]);

        $fusedQsr = qsr_token()->getFormattedAmount(Plasma::isActive()->sum('amount'), 0);
        Cache::put('fused-qsr', $fusedQsr);

        (new SetBlockAsProcessed($this->block))->execute();

        \App\Events\Nom\Plasma\Fuse::dispatch($this->block, $blockData);
    }
}
