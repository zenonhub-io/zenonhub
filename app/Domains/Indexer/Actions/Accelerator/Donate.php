<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use Illuminate\Support\Facades\Log;

class Donate extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        if (! $this->validateAction($accountBlock)) {
            Log::info('Contract Method Processor - Accelerator: Donate failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): bool
    {
        [$accountBlock] = func_get_args();

        return true;
    }
}
