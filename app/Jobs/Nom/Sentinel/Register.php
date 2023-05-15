<?php

namespace App\Jobs\Nom\Sentinel;

use App;
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
        $znn = App::make('zenon.api');
        $sentinelData = $znn->sentinel->getByOwner($this->block->account->address)['data'];

        if (! $sentinelData) {
            return;
        }

        $sentinel = Sentinel::whereHas('owner', function ($q) {
            $q->where('address', $this->block->account->address);
        })->first();

        if (! $sentinel) {
            $sentinel = Sentinel::create([
                'chain_id' => $this->block->chain->id,
                'owner_id' => $this->block->account->id,
                'created_at' => $sentinelData->registrationTimestamp,
            ]);
        }

        $sentinel->created_at = $sentinelData->registrationTimestamp;
        $sentinel->revoked_at = null;
        $sentinel->save();

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
