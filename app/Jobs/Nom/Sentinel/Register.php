<?php

namespace App\Jobs\Nom\Sentinel;

use App\Actions\SetBlockAsProcessed;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Sentinel;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Register implements ShouldQueue
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
        $sentinel = Sentinel::where('owner_id', $this->block->account->id)->first();

        if (! $sentinel) {
            $sentinel = Sentinel::create([
                'chain_id' => $this->block->chain->id,
                'owner_id' => $this->block->account->id,
                'created_at' => $this->block->created_at,
            ]);
        }

        $sentinel->created_at = $this->block->created_at;
        $sentinel->revoked_at = null;
        $sentinel->save();

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
