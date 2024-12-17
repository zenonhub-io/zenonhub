<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Liquidity;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Stake\StartStake;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Stake as StakeModel;
use Illuminate\Support\Facades\Log;

class LiquidityStake extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('token');
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Liquidity: LiquidityStake failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $stake = StakeModel::create([
            'chain_id' => $accountBlock->chain_id,
            'account_id' => $accountBlock->account_id,
            'token_id' => $accountBlock->token_id,
            'account_block_id' => $accountBlock->id,
            'amount' => $accountBlock->amount,
            'duration' => $blockData['durationInSec'],
            'hash' => $accountBlock->hash,
            'started_at' => $accountBlock->created_at,
        ]);

        StartStake::dispatch($accountBlock, $stake);

        Log::info('Contract Method Processor - Liquidity: LiquidityStake complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'stake' => $stake,
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

        if ($accountBlock->token->token_standard !== NetworkTokensEnum::LP_ZNN_ETH->value) {
            throw new IndexerActionValidationException('Invalid stake token');
        }

        if ($accountBlock->amount <= 0) {
            throw new IndexerActionValidationException('Invalid stake amount');
        }

        if ($accountBlock->data->decoded['durationInSec'] <= 0) {
            throw new IndexerActionValidationException('Invalid stake duration');
        }
    }
}
