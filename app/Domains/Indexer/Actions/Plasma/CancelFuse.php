<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Plasma;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Plasma\EndFuse;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Plasma;
use Illuminate\Support\Facades\Log;

class CancelFuse extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $plasma = Plasma::findBy('hash', $blockData['id']);

        if (! $plasma || ! $this->validateAction($accountBlock, $plasma)) {
            Log::info('Contract Method Processor - Plasma: CancelFuse failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        $plasma->ended_at = $accountBlock->created_at;
        $plasma->save();

        EndFuse::dispatch($accountBlock, $plasma);

        Log::info('Contract Method Processor - Plasma: CancelFuse complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'plasma' => $plasma,
        ]);

        //\App\Events\Nom\Plasma\CancelFuse::dispatch($this->block, $blockData);

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): bool
    {
        /**
         * @var AccountBlock $accountBlock
         * @var Plasma $plasma
         */
        [$accountBlock, $plasma] = func_get_args();

        if ($accountBlock->account_id !== $plasma->from_account_id) {
            return false;
        }

        if ($plasma->started_at->addHours(config('nom.plasma.expiration')) >= $accountBlock->created_at) {
            return false;
        }

        return true;
    }
}
