<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;

class Donate extends AbstractContractMethodProcessor
{
    public AccountBlock $block;

    public function handle(AccountBlock $accountBlock): void
    {

    }
}
