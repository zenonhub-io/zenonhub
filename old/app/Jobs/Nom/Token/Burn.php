<?php

declare(strict_types=1);

namespace App\Jobs\Nom\Token;

use App\Actions\SetBlockAsProcessed;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\TokenBurn;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Burn implements ShouldQueue
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
        TokenBurn::create([
            'chain_id' => $this->block->chain->id,
            'token_id' => $this->block->token->id,
            'account_id' => $this->block->account->id,
            'account_block_id' => $this->block->id,
            'amount' => $this->block->amount,
            'created_at' => $this->block->created_at,
        ]);

        $this->updateTokenSupply();

        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function updateTokenSupply()
    {
        $token = $this->block->token;
        $data = $token->raw_json;
        $token->total_supply = $data->totalSupply;
        $token->max_supply = $data->maxSupply;
        $token->save();
    }
}
