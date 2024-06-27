<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Bridge\NetworkMetadataSet;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeAdmin;
use App\Domains\Nom\Models\BridgeNetwork;
use Illuminate\Support\Facades\Log;

class SetNetworkMetadata extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $bridgeNetwork = BridgeNetwork::findByNetworkChain($blockData['networkClass'], $blockData['chainId']);

        try {
            $this->validateAction($accountBlock, $bridgeNetwork);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Bridge: SetNetworkMetadata failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $bridgeNetwork->meta_data = json_decode($blockData['metadata']);
        $bridgeNetwork->save();

        NetworkMetadataSet::dispatch($accountBlock, $bridgeNetwork);

        Log::info('Contract Method Processor - Bridge: SetNetworkMetadata complete', [
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

        if (! $bridgeAdmin->account_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Action sent from non admin');
        }

        if (! $bridgeNetwork) {
            throw new IndexerActionValidationException('Invalid bridgeNetwork');
        }
    }
}
