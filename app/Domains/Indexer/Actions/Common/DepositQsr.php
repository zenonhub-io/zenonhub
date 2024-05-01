<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Common;

use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AccountBlock;

class DepositQsr extends AbstractIndexerAction
{
    public function handle(AccountBlock $accountBlock): void
    {

    }
}
