<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Bridge\TokenUnwraped;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeNetwork;
use App\Domains\Nom\Models\BridgeUnwrap;
use App\Domains\Nom\Models\Token;
use Illuminate\Support\Facades\Log;

class UnwrapToken extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $network = BridgeNetwork::findByNetworkChain($blockData['networkClass'], $blockData['chainId']);
        $token = $network?->tokens()
            ->wherePivot('token_address', $blockData['toAddress'])
            ->first();

        try {
            $this->validateAction($accountBlock, $network, $token);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Bridge: UnwrapToken failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $unwrap = BridgeUnwrap::updateOrCreate([
            'transaction_hash' => $blockData['transactionHash'],
            'log_index' => $blockData['logIndex'],
        ], [
            'bridge_network_id' => $network->id,
            'to_account_id' => load_account($blockData['toAddress'])->id,
            'token_id' => $token->id,
            'account_block_id' => $accountBlock->id,
            'signature' => $blockData['signature'],
            'amount' => $blockData['amount'],
            'updated_at' => $accountBlock->created_at,
        ]);

        if (! $unwrap->created_at) {
            $unwrap->created_at = $accountBlock->created_at;
            $unwrap->save();
        }

        $unwrap->setFromAddress();

        TokenUnwraped::dispatch($accountBlock, $unwrap);

        Log::info('Contract Method Processor - Bridge: UnwrapToken complete', [
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
         * @var BridgeNetwork $bridgeNetwork
         * @var Token $token
         */
        [$accountBlock] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if (! $bridgeNetwork) {
            throw new IndexerActionValidationException('Invalid bridge network');
        }

        if (! $token) {
            throw new IndexerActionValidationException('Invalid token');
        }

        if (! $token->pivot->is_redeemable) {
            throw new IndexerActionValidationException('Token is not redeemable');
        }
    }
}
