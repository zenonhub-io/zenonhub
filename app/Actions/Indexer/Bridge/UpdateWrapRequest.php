<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Bridge;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Bridge\WrapRequestUpdated;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeWrap;
use Illuminate\Support\Facades\Log;

class UpdateWrapRequest extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $wrap = BridgeWrap::whereRelation('accountBlock', 'hash', $blockData['id'])
            ->first();

        try {
            $this->validateAction($accountBlock, $wrap);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Bridge: UpdateWrapRequest failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $wrap->signature = $blockData['signature'];
        $wrap->updated_at = $accountBlock->created_at;
        $wrap->save();

        WrapRequestUpdated::dispatch($accountBlock, $wrap);

        Log::info('Contract Method Processor - Bridge: UpdateWrapRequest complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    /**
     * @throws IndexerActionValidationException
     */
    public function validateAction(): void
    {
        /**
         * @var AccountBlock $accountBlock
         * @var BridgeWrap $wrap
         */
        [$accountBlock, $wrap] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if (! $wrap) {
            throw new IndexerActionValidationException('Invalid wrap');
        }
    }
}
