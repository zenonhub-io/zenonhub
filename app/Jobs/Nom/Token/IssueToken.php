<?php

namespace App\Jobs\Nom\Token;

use App;
use App\Actions\SetBlockAsProcessed;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Token;
use DigitalSloth\ZnnPhp\Utilities;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IssueToken implements ShouldQueue
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
        $zts = Utilities::ztsFromHash($this->block->hash);
        $tokenData = $znn->token->getByZts($zts)['data'];
        $token = Token::whereZts($tokenData->tokenStandard)->first();

        if (! $token) {
            $token = Token::create([
                'chain_id' => $this->block->chain->id,
                'owner_id' => $this->block->account->id,
                'name' => $tokenData->name,
                'symbol' => $tokenData->symbol,
                'domain' => $tokenData->domain,
                'token_standard' => $tokenData->tokenStandard,
                'total_supply' => $tokenData->totalSupply,
                'max_supply' => $tokenData->maxSupply,
                'decimals' => $tokenData->decimals,
                'is_burnable' => $tokenData->isBurnable,
                'is_mintable' => $tokenData->isMintable,
                'is_utility' => $tokenData->isUtility,
                'created_at' => $this->block->created_at,
            ]);
        }

        $token->is_burnable = $tokenData->isBurnable;
        $token->is_mintable = $tokenData->isMintable;
        $token->is_utility = $tokenData->isUtility;
        $token->created_at = $this->block->created_at;
        $token->save();

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
