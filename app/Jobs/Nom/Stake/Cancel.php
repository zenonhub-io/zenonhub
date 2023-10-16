<?php

namespace App\Jobs\Nom\Stake;

use App\Actions\SetBlockAsProcessed;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Stake;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

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

        $stake = Stake::where('hash', $blockData['id'])->first();

        if ($stake) {
            $stake->ended_at = $this->block->created_at;
            $stake->save();
        }

        $totalZnnStaked = Stake::isActive()->isZnn()->sum('amount');
        $stakedZnn = znn_token()->getDisplayAmount($totalZnnStaked, 0);
        Cache::put('staked-znn', $stakedZnn);

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
