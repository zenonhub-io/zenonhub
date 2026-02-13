<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Token;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Token\TokenBurned;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\TokenBurn;
use Illuminate\Support\Facades\DB;
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
            Log::error('Contract Method Processor - Token: Burn failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $burn = null;

        DB::transaction(function () use ($accountBlock, &$burn) {
            $burn = TokenBurn::create([
                'chain_id' => $accountBlock->chain_id,
                'token_id' => $accountBlock->token_id,
                'account_id' => $accountBlock->account_id,
                'account_block_id' => $accountBlock->id,
                'amount' => $accountBlock->amount,
                'created_at' => $accountBlock->created_at,
            ]);

            $burn->token->update([
                'total_supply' => bcsub($burn->token->total_supply, $burn->amount),
            ]);

            // For non-mintable coins, drop MaxSupply as well
            if (! $burn->token->is_mintable) {
                $burn->token->update([
                    'max_supply' => bcsub($burn->token->max_supply, $burn->amount),
                ]);
            }
        });

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
}
