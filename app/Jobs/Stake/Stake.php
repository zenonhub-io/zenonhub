<?php

namespace App\Jobs\Stake;

use App;
use Cache;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Staker;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Stake implements ShouldQueue
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

        Staker::create([
            'account_id' => $this->block->account_id,
            'amount' => $this->block->amount,
            'duration' => $blockData['durationInSec'],
            'hash' => $this->block->hash,
            'started_at' => $this->block->created_at,
        ]);

        $stakedZnn = znn_token()->getDisplayAmount(Staker::isActive()->sum('amount'), 0);
        Cache::put('staked-znn', $stakedZnn);
    }
}
