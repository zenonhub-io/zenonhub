<?php

declare(strict_types=1);

namespace App\Jobs\Nom\Stake;

use App\Actions\SetBlockAsProcessed;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Stake as StakeModel;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

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

        StakeModel::create([
            'chain_id' => $this->block->chain->id,
            'account_id' => $this->block->account_id,
            'token_id' => $this->block->token_id,
            'amount' => $this->block->amount,
            'duration' => $blockData['durationInSec'],
            'hash' => $this->block->hash,
            'started_at' => $this->block->created_at,
        ]);

        $totalZnnStaked = StakeModel::isActive()->isZnn()->sum('amount');
        $stakedZnn = app('znnToken')->getFormattedAmount($totalZnnStaked, 0);
        Cache::put('staked-znn', $stakedZnn);

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
