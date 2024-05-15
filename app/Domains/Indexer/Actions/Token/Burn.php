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
        $this->accountBlock = $accountBlock;

        if (! $this->validateAction($accountBlock->token)) {
            return;
        }

        $burn = TokenBurn::create([
            'chain_id' => $accountBlock->chain_id,
            'token_id' => $accountBlock->token_id,
            'account_id' => $accountBlock->account_id,
            'account_block_id' => $accountBlock->id,
            'amount' => $accountBlock->amount,
            'created_at' => $accountBlock->created_at,
        ]);

        $this->updateTokenSupply($burn);

        TokenBurned::dispatch($accountBlock, $burn);
    }

    protected function validateAction(): bool
    {
        [$token] = func_get_args();

        if ($this->accountBlock->amount <= 0) {
            return false;
        }

        return $token->is_mintable && $token->owner_id === $this->accountBlock->account_id;
    }

    private function updateTokenSupply(TokenBurn $burn): void
    {
        $token = $burn->token;
        $token->total_supply -= $burn->amount;

        // For non-mintable coins, drop MaxSupply as well
        if (! $token->is_mintable) {
            $token->max_supply -= $burn->amount;
        }

        $token->save();
    }
}
