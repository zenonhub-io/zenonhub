<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Token\TokenBurned;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\TokenBurn;
use Illuminate\Support\Facades\Log;

class Burn extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('token');
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Token: Burn failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
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

    /**
     * @throws IndexerActionValidationException
     */
    public function validateAction(): void
    {
        /**
         * @var AccountBlock $accountBlock
         */
        [$accountBlock] = func_get_args();

        if ($accountBlock->amount <= 0) {
            throw new IndexerActionValidationException('Amount is too small');
        }

        if (! $accountBlock->token->is_burnable && $accountBlock->token->owner_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Token is not burnable, or owner doesnt match');
        }
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