<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Bridge;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Bridge\UnwrapRedeemed;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeUnwrap;
use Illuminate\Support\Facades\Log;

class Redeem extends AbstractContractMethodProcessor
{
    public BridgeUnwrap $unwrap;

    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $unwrap = BridgeUnwrap::whereTxHashLog($blockData['transactionHash'], $blockData['logIndex'])
            ->whereUnredeemed()
            ->first();

        try {
            $this->validateAction($accountBlock, $unwrap);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Bridge: Redeem failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $unwrap->redeemed_at = $accountBlock->created_at;
        $unwrap->save();

        UnwrapRedeemed::dispatch($accountBlock, $unwrap);

        Log::error('Contract Method Processor - Bridge: Redeem complete', [
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
         * @var BridgeUnwrap $unwrap
         */
        [$accountBlock, $unwrap] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if (! $unwrap) {
            throw new IndexerActionValidationException('Invalid unwrap');
        }
    }
}
