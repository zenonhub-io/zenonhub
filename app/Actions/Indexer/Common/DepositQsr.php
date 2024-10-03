<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Common;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Models\Nom\AccountBlock;

class DepositQsr extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
    }

    public function validateAction(): void {}
}
