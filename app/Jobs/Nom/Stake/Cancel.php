<?php

namespace App\Jobs\Nom\Stake;

use App\Actions\SetBlockAsProcessed;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Staker;
use Cache;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Cancel implements ShouldQueue
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

        $stake = Staker::where('hash', $blockData['id'])->first();

        if ($stake) {
            $stake->ended_at = $this->block->created_at;
            $stake->save();
        }

        $stakedZnn = znn_token()->getDisplayAmount(Staker::isActive()->sum('amount'), 0);
        Cache::put('staked-znn', $stakedZnn);

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
