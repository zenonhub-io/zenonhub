<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Bridge\TokenUnwraped;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeNetwork;
use App\Domains\Nom\Models\BridgeNetworkToken;
use App\Domains\Nom\Models\BridgeUnwrap;
use Illuminate\Support\Facades\Log;
use Throwable;

class UnwrapToken extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Bridge: UnwrapToken failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        // Logic here

        TokenUnwraped::dispatch($accountBlock);

        Log::info('Contract Method Processor - Bridge: UnwrapToken complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
        ]);

        $this->setBlockAsProcessed($accountBlock);

        //        $this->accountBlock = $accountBlock;
        //        $blockData = $accountBlock->data->decoded;
        //
        //        try {
        //            $this->processUnwrap();
        //        } catch (Throwable $exception) {
        //            Log::warning('Unable to process unwrap: ' . $accountBlock->hash);
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

    private function processUnwrap(): void
    {
        $data = $accountBlock->data->decoded;
        $network = BridgeNetwork::findByNetworkChain($data['networkClass'], $data['chainId']);
        $account = load_account($data['toAddress']);
        $bridgeToken = BridgeNetworkToken::findByTokenAddress($network->id, $data['tokenAddress']);

        $unwrap = BridgeUnwrap::updateOrCreate([
            'transaction_hash' => $data['transactionHash'],
            'log_index' => $data['logIndex'],
        ], [
            'bridge_network_id' => $network->id,
            'bridge_network_token_id' => $bridgeToken->id,
            'to_account_id' => $account->id,
            'token_id' => $bridgeToken->token->id,
            'account_block_id' => $accountBlock->id,
            'signature' => $data['signature'],
            'amount' => $data['amount'],
            'updated_at' => $accountBlock->created_at,
        ]);

        if (! $unwrap->created_at) {
            $unwrap->created_at = $accountBlock->created_at;
            $unwrap->save();
        }

        $unwrap->setFromAddress();
    }
}
