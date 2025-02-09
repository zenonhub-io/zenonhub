<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Pillar;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Pillar\AccountUndelegated;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Undelegate extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('account');
        $blockData = $accountBlock->data->decoded;

        $delegations = $accountBlock->account
            ->delegations()
            ->wherePivotNull('ended_at')
            ->get();

        try {
            $this->validateAction($accountBlock, $delegations);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Pillar: Undelegate failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $delegations->each(function ($delegation) use ($accountBlock) {

            Cache::forget($delegation->cacheKey('pillar-rank', 'updated_at'));

            $accountBlock->account
                ->delegations()
                ->updateExistingPivot($delegation->id, [
                    'ended_at' => $accountBlock->created_at,
                ]);

            AccountUndelegated::dispatch($accountBlock, $accountBlock->account, $delegation);

        });

        Log::info('Contract Method Processor - Pillar: Undelegate complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'account' => $accountBlock->account->address,
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
         * @var Collection $delegations
         */
        [$accountBlock, $delegations] = func_get_args();

        if (! $delegations->count()) {
            throw new IndexerActionValidationException('No delegation found');
        }
    }
}
