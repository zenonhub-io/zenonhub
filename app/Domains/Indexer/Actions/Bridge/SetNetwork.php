<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Bridge\NetworkSet;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeAdmin;
use App\Domains\Nom\Models\BridgeNetwork;
use App\Domains\Nom\Models\Chain;
use Illuminate\Support\Facades\Log;

class SetNetwork extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Bridge: SetNetwork failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $chain = Chain::where('chain_identifier', $blockData['chainId'])->first();
        $bridgeNetwork = BridgeNetwork::firstOrNew([
            'chain_id' => $chain->id,
            'network_class' => $blockData['networkClass'],
            'chain_identifier' => $blockData['chainId'],
        ]);

        $bridgeNetwork->name = $blockData['name'];
        $bridgeNetwork->contract_address = $blockData['contractAddress'];
        $bridgeNetwork->meta_data = json_decode($blockData['metadata']);
        $bridgeNetwork->updated_at = $accountBlock->created_at;

        if (! $bridgeNetwork->created_at) {
            $bridgeNetwork->created_at = $accountBlock->created_at;
        }

        $bridgeNetwork->save();

        NetworkSet::dispatch($accountBlock, $bridgeNetwork);

        Log::info('Contract Method Processor - Bridge: SetNetwork complete', [
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
         */
        [$accountBlock] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        $bridgeAdmin = BridgeAdmin::getActiveAdmin();

        if (! $bridgeAdmin->account_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Action sent from non admin');
        }

        if ($blockData['name'] < 3 || $blockData['name'] > 32) {
            throw new IndexerActionValidationException('Invalid name length');
        }

        if ($blockData['networkClass'] < 1 || $blockData['chainId'] < 1) {
            throw new IndexerActionValidationException('Invalid networkClass or chainId');
        }

        $chain = Chain::where('chain_identifier', $blockData['chainId'])->first();
        if (! $chain) {
            throw new IndexerActionValidationException('Invalid chain identifier');
        }

        //        if (! isHex($blockData['contractAddress'])) {
        //            throw new IndexerActionValidationException('Invalid contractAddress');
        //        }
    }
}
