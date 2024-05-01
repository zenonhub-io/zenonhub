<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AccountBlock;

class Donate extends AbstractIndexerAction
{
    public AccountBlock $block;

    public function handle(AccountBlock $accountBlock): void
    {

    }
}
