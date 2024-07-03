<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Common;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;

class DepositQsr extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
    }

    public function validateAction(): void
    {
    }
}
