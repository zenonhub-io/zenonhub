<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Bridge;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Bridge\TokenPairRemoved;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeAdmin;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\Token;
use Illuminate\Support\Facades\Log;

class RemoveTokenPair extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $bridgeNetwork = BridgeNetwork::findByNetworkChain($blockData['networkClass'], $blockData['chainId']);

        try {
            $this->validateAction($accountBlock, $bridgeNetwork);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Bridge: RemoveTokenPair failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $token = Token::firstWhere('token_standard', $blockData['tokenStandard']);
        $bridgeNetwork->tokens()->detach($token);

        TokenPairRemoved::dispatch($accountBlock, $bridgeNetwork, $token);

        Log::error('Contract Method Processor - Bridge: RemoveTokenPair complete', [
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
         */
        [$accountBlock, $bridgeNetwork] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        $bridgeAdmin = BridgeAdmin::getActiveAdmin();

        if ($bridgeAdmin->account_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Action sent from non admin');
        }

        if (! $bridgeNetwork) {
            throw new IndexerActionValidationException('Invalid bridgeNetwork');
        }
    }
}
