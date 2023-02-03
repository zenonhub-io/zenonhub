<?php

namespace App\Jobs\Sentinel;

use App;
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
        $sentinel = Sentinel::whereHas('owner', function ($q) {
            $q->where('address', $this->block->account->address);
        })->first();

        $sentinelData = $this->getSentinelData();

        if (! $sentinelData) {
            return;
        }

        if (! $sentinel) {
            $sentinel = Sentinel::create([
                'owner_id' => $this->block->account->id,
                'is_revocable' => $sentinelData->isRevocable,
                'revoke_cooldown' => $sentinelData->revokeCooldown,
                'is_active' => $sentinelData->active,
                'created_at' => $sentinelData->registrationTimestamp,
            ]);
        }

        $sentinel->is_revocable = $sentinelData->isRevocable;
        $sentinel->revoke_cooldown = $sentinelData->revokeCooldown;
        $sentinel->is_active = $sentinelData->active;
        $sentinel->created_at = $sentinelData->registrationTimestamp;
        $sentinel->save();
    }

    private function getSentinelData()
    {
        $znn = App::make('zenon.api');
        return $znn->sentinel->getByOwner($this->block->account->address)['data'];
    }
}
