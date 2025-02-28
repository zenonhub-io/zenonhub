<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Stake;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Stake\StartStake;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Stake as StakeModel;
use Illuminate\Support\Facades\Log;

class Stake extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('token');
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Stake: Stake failed', [
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

        Log::info('Contract Method Processor - Stake: Stake complete', [
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
        $blockData = $accountBlock->data->decoded;

        if ($accountBlock->token->token_standard !== app('znnToken')->token_standard) {
            throw new IndexerActionValidationException('Invalid stake token');
        }

        if ($accountBlock->amount < config('nom.stake.minAmount')) {
            throw new IndexerActionValidationException('Invalid stake amount');
        }

        if (
            $blockData['durationInSec'] < config('nom.stake.timeMinSec') ||
            $blockData['durationInSec'] > config('nom.stake.timeMaxSec')
        ) {
            throw new IndexerActionValidationException('Invalid stake duration');
        }
    }
}
