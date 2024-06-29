<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Bridge\UnwrapRedeemed;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeUnwrap;
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
            Log::info('Contract Method Processor - Bridge: Redeem failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $unwrap->redeemed_at = $accountBlock->created_at;
        $unwrap->save();

        UnwrapRedeemed::dispatch($accountBlock, $unwrap);

        Log::info('Contract Method Processor - Bridge: Redeem complete', [
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
        [$accountBlock] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if (! $unwrap) {
            throw new IndexerActionValidationException('Invalid unwrap');
        }
    }
}
