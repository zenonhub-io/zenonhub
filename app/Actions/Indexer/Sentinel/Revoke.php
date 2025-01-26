<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Sentinel;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Sentinel\SentinelRevoked;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Sentinel;
use Illuminate\Support\Facades\Log;

class Revoke extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $sentinel = Sentinel::whereOwner($accountBlock->account_id)->whereActive()->first();

        try {
            $this->validateAction($accountBlock, $sentinel);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Sentinel: Revoke failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $sentinel->revoked_at = $accountBlock->created_at;
        $sentinel->save();

        SentinelRevoked::dispatch($accountBlock, $sentinel);

        Log::info('Contract Method Processor - Sentinel: Revoke complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'sentinel' => $sentinel,
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
         * @var Sentinel $sentinel
         */
        [$accountBlock, $sentinel] = func_get_args();

        if (! $sentinel) {
            throw new IndexerActionValidationException('Invalid sentinel');
        }

        if (! $sentinel->getIsRevokableAttribute($accountBlock->created_at)) {
            throw new IndexerActionValidationException('Sentinel not revocable');
        }
    }
}
