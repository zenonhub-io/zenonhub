<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Bridge\UnwrapRequestRevoked;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeAdmin;
use App\Domains\Nom\Models\BridgeUnwrap;
use Illuminate\Support\Facades\Log;

class RevokeUnwrapRequest extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $unwrap = BridgeUnwrap::findByTxHashLog($blockData['transactionHash'], $blockData['logIndex']);

        try {
            $this->validateAction($accountBlock, $unwrap);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Bridge: RevokeUnwrapRequest failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $unwrap->revoked_at = $accountBlock->created_at;
        $unwrap->save();

        UnwrapRequestRevoked::dispatch($accountBlock, $unwrap);

        Log::info('Contract Method Processor - Bridge: RemoveUnwrapRequest complete', [
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

        $bridgeAdmin = BridgeAdmin::getActiveAdmin();

        if ($bridgeAdmin->account_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Action sent from non admin');
        }
    }
}