<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Token\TokenBurned;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\TokenBurn;

class Burn extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $burn = TokenBurn::create([
            'chain_id' => $accountBlock->chain->id,
            'token_id' => $accountBlock->token->id,
            'account_id' => $accountBlock->account->id,
            'account_block_id' => $accountBlock->id,
            'amount' => $accountBlock->amount,
            'created_at' => $accountBlock->created_at,
        ]);

        $this->updateTokenSupply($burn);

        TokenBurned::dispatch($accountBlock, $burn);
    }

    private function updateTokenSupply(TokenBurn $burn): void
    {
        $token = $burn->token;
        $token->total_supply -= $burn->amount;
        $token->save();
    }
}
