<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Token\TokenMinted;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\TokenMint;

class Mint extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        $mint = TokenMint::create([
            'chain_id' => $accountBlock->chain_id,
            'token_id' => load_token($blockData['tokenStandard'])->id,
            'issuer_id' => $accountBlock->account_id,
            'receiver_id' => load_account($blockData['receiveAddress'])->id,
            'account_block_id' => $accountBlock->id,
            'amount' => $blockData['amount'],
            'created_at' => $accountBlock->created_at,
        ]);

        $this->updateTokenSupply($mint);

        TokenMinted::dispatch($accountBlock, $mint);
    }

    private function updateTokenSupply(TokenMint $mint): void
    {
        $token = $mint->token;
        $token->total_supply += $mint->amount;
        $token->save();
    }
}
