<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Stake;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Stake\EndStake;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Stake;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Cancel extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $stake = Stake::whereRelation('accountBlock', 'hash', $blockData['id'])->first();

        try {
            $this->validateAction($accountBlock, $stake);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Stake: Cancel failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        DB::transaction(function () use ($accountBlock, $stake) {
            $stake->ended_at = $accountBlock->created_at;
            $stake->save();

            $stake->account->update([
                'znn_staked' => bcsub($stake->account->znn_staked, $stake->amount),
            ]);
        });

        EndStake::dispatch($accountBlock, $stake);

        Log::info('Contract Method Processor - Stake: Cancel complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $stake,
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
         * @var \App\Actions\Indexer\Stake\Stake $stake
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
