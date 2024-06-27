<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Bridge\UnwrapRequestRevoked;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeUnwrap;
use Illuminate\Support\Facades\Log;
use Throwable;

class RevokeUnwrapRequest extends AbstractContractMethodProcessor
{
    public BridgeUnwrap $unwrap;

    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Bridge: RevokeUnwrapRequest failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        // Logic here

        UnwrapRequestRevoked::dispatch($accountBlock);

        Log::info('Contract Method Processor - Bridge: RemoveUnwrapRequest complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
        ]);

        $this->setBlockAsProcessed($accountBlock);

        //        $this->accountBlock = $accountBlock;
        //        $blockData = $accountBlock->data->decoded;
        //
        //        try {
        //            $this->loadUnwrap();
        //            $this->processRevokeUnwrap();
        //        } catch (Throwable $exception) {
        //            Log::warning('Error revoking unwrap request ' . $accountBlock->hash);
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

    private function loadUnwrap(): void
    {
        $data = $accountBlock->data->decoded;
        $this->unwrap = BridgeUnwrap::where('transaction_hash', $data['transactionHash'])
            ->where('log_index', $data['logIndex'])
            ->sole();
    }

    private function processRevokeUnwrap(): void
    {
        $this->unwrap->revoked_at = $accountBlock->created_at;
        $this->unwrap->save();
    }
}
