<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Models\AccountBlock;
use Illuminate\Support\Facades\Log;

class Donate extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Accelerator: Donate failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): void
    {
        [$accountBlock] = func_get_args();
    }
}
