<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Liquidity;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Stake\EndStake;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Stake;
use Illuminate\Support\Facades\Log;

class CancelLiquidityStake extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $stake = Stake::firstWhere('hash', $blockData['id']);

        try {
            $this->validateAction($accountBlock, $stake);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Liquidity: CancelLiquidityStake failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $stake->ended_at = $accountBlock->created_at;
        $stake->save();

        EndStake::dispatch($accountBlock, $stake);

        Log::info('Contract Method Processor - Liquidity: CancelLiquidityStake complete', [
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
         * @var Stake $stake
         */
        [$accountBlock, $stake] = func_get_args();

        if (! $stake) {
            throw new IndexerActionValidationException('Invalid stake');
        }

        if ($stake->account_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Account is not stake owner');
        }

        if ($stake->end_date > $accountBlock->created_at) {
            throw new IndexerActionValidationException('Stake end date in the future');
        }
    }
}
