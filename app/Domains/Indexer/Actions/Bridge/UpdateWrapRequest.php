<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Bridge\WrapRequestUpdated;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeWrap;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateWrapRequest extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Bridge: UpdateWrapRequest failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        // Logic here

        WrapRequestUpdated::dispatch($accountBlock);

        Log::info('Contract Method Processor - Bridge: UpdateWrapRequest complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
        ]);

        $this->setBlockAsProcessed($accountBlock);

        //        $this->accountBlock = $accountBlock;
        //        $blockData = $accountBlock->data->decoded;
        //
        //        try {
        //            $this->loadWrap();
        //            $this->processUpdate();
        //        } catch (Throwable $exception) {
        //            Log::warning('Error updating wrap request ' . $accountBlock->hash);
        //            Log::debug($exception);
        //
        //            return;
        //        }

    }

    /**
     * @throws IndexerActionValidationException
     */
    public function validateAction(): void
    {
        /**
         * @var AccountBlock $accountBlock
         */
        [$accountBlock] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        //throw new IndexerActionValidationException('');
    }

    private function loadWrap(): void
    {
        $this->wrap = BridgeWrap::whereRelation('accountBlock', 'hash', $this->blockData['id'])
            ->sole();
    }

    private function processUpdate(): void
    {
        $this->wrap->signature = $this->blockData['signature'];
        $this->wrap->updated_at = $accountBlock->created_at;
        $this->wrap->save();
    }
}
