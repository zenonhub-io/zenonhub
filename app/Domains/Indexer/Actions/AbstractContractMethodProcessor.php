<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions;

use App\Domains\Nom\Models\AccountBlock;
use Lorisleiva\Actions\Concerns\AsAction;

abstract class AbstractContractMethodProcessor
{
    use AsAction;

    abstract public function handle(AccountBlock $accountBlock): void;

    public function validateAction(): bool
    {
        return true;
    }

    public function setBlockAsProcessed(AccountBlock $accountBlock): void
    {
        $accountBlock->data->is_processed = true;
        $accountBlock->data->save();
    }
}
