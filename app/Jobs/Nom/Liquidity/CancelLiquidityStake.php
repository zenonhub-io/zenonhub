<?php

namespace App\Jobs\Nom\Liquidity;

use App\Actions\SetBlockAsProcessed;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Stake;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CancelLiquidityStake implements ShouldQueue
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

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
