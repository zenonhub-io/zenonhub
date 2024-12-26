<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Plasma;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Plasma\EndFuse;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Plasma;
use Illuminate\Support\Facades\Log;

class CancelFuse extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $plasma = Plasma::firstWhere('hash', $blockData['id']);

        try {
            $this->validateAction($accountBlock, $plasma);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Plasma: CancelFuse failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $plasma->ended_at = $accountBlock->created_at;
        $plasma->save();

        EndFuse::dispatch($accountBlock, $plasma);

        Log::info('Contract Method Processor - Plasma: CancelFuse complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'plasma' => $plasma,
        ]);

        //\App\Events\Nom\Plasma\CancelFuse::dispatch($this->block, $blockData);

        $this->setBlockAsProcessed($accountBlock);
    }

    /**
     * @throws IndexerActionValidationException
     */
    public function validateAction(): void
    {
        /**
         * @var AccountBlock $accountBlock
         * @var Plasma $plasma
         */
        [$accountBlock, $plasma] = func_get_args();

        if (! $plasma) {
            throw new IndexerActionValidationException('Invalid plasma');
        }

        if ($accountBlock->account_id !== $plasma->from_account_id) {
            throw new IndexerActionValidationException('Account is not plasma owner');
        }

        // Plasma expiration is actually based on momentum height, so this is a bit of a workaround
        if ($plasma->started_at->addSeconds(config('nom.plasma.expiration') * 10) >= $accountBlock->created_at) {
            throw new IndexerActionValidationException('Plasma not yet cancelable');
        }
    }
}
