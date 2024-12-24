<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Accelerator;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use Illuminate\Support\Facades\Log;

class Donate extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Accelerator: Donate failed', [
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
