<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\TokenBurn;

class Burn extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        TokenBurn::create([
            'chain_id' => $this->accountBlock->chain->id,
            'token_id' => $this->accountBlock->token->id,
            'account_id' => $this->accountBlock->account->id,
            'account_block_id' => $this->accountBlock->id,
            'amount' => $this->accountBlock->amount,
            'created_at' => $this->accountBlock->created_at,
        ]);

        $this->updateTokenSupply();

    }

    private function updateTokenSupply()
    {
        $token = $this->accountBlock->token;
        $data = $token->raw_json;
        $token->total_supply = $data->totalSupply;
        $token->max_supply = $data->maxSupply;
        $token->save();
    }
}
