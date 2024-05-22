<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Token\TokenBurned;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\TokenBurn;
use Illuminate\Support\Facades\Log;

class Burn extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('token');
        $blockData = $accountBlock->data?->decoded;

        if (! $this->validateAction($accountBlock)) {
            Log::info('Contract Method Processor - Token: Burn failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

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

        Log::info('Contract Method Processor - Token: Burn complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'burn' => $burn,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): bool
    {
        /**
         * @var AccountBlock $accountBlock
         */
        [$accountBlock] = func_get_args();

        if ($accountBlock->amount <= 0) {
            return false;
        }

        if (! $accountBlock->token->is_burnable) {
            return false;
        }

        if ($accountBlock->token->owner_id === $accountBlock->account_id) {
            return false;
        }

        return true;
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
