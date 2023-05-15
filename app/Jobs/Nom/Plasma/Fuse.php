<?php

namespace App\Jobs\Nom\Plasma;

use App\Actions\SetBlockAsProcessed;
use App\Classes\Utilities;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Fusion;
use Cache;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $toAccount = Utilities::loadAccount($blockData['address']);

        Fusion::create([
            'chain_id' => $this->block->chain->id,
            'from_account_id' => $this->block->account_id,
            'to_account_id' => $toAccount->id,
            'amount' => $this->block->amount,
            'hash' => $this->block->hash,
            'started_at' => $this->block->created_at,
            'ended_at' => null,
        ]);

        $fusedQsr = qsr_token()->getDisplayAmount(Fusion::isActive()->sum('amount'), 0);
        Cache::put('fused-qsr', $fusedQsr);

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
