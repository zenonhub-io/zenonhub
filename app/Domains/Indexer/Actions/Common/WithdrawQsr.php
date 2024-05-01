<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Common;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;

class WithdrawQsr extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {

    }
}
