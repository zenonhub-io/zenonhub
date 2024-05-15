<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Plasma;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Plasma\EndFuse;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Plasma;

class CancelFuse extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $this->accountBlock = $accountBlock;
        $blockData = $accountBlock->data->decoded;
        $plasma = Plasma::findBy('hash', $blockData['id']);

        if (! $plasma || ! $this->validateAction($plasma)) {
            return;
        }

        $plasma->ended_at = $accountBlock->created_at;
        $plasma->save();

        EndFuse::dispatch($accountBlock, $plasma);

        //\App\Events\Nom\Plasma\CancelFuse::dispatch($this->block, $blockData);
    }

    protected function validateAction(): bool
    {
        [$plasma] = func_get_args();

        if ($this->accountBlock->account_id !== $plasma->from_account_id) {
            return false;
        }

        if ($plasma->started_at->addHours(config('nom.plasma.expiration')) > now()) {
            return false;
        }

        return true;
    }
}
