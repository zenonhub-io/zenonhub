<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Bridge\TokenWrapped;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeNetwork;
use App\Domains\Nom\Models\BridgeWrap;
use App\Domains\Nom\Models\Token;
use Illuminate\Support\Facades\Log;

class WrapToken extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $network = BridgeNetwork::findByNetworkChain($blockData['networkClass'], $blockData['chainId']);
        $token = $network?->tokens()->where('id', $accountBlock->token_id)->first();

        try {
            $this->validateAction($accountBlock, $network, $token);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Bridge: WrapToken failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $wrap = BridgeWrap::create([
            'bridge_network_id' => $network->id,
            'account_id' => $accountBlock->account_id,
            'token_id' => $accountBlock->token_id,
            'account_block_id' => $accountBlock->id,
            'to_address' => $blockData['toAddress'],
            'amount' => $accountBlock->amount,
            'created_at' => $accountBlock->created_at,
        ]);

        TokenWrapped::dispatch($accountBlock, $wrap);

        Log::info('Contract Method Processor - Bridge: WrapToken complete', [
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

        if (! $token->pivot->is_bridgeable) {
            throw new IndexerActionValidationException('Token is not redeemable');
        }

        if (bccomp($accountBlock->amount, $token->pivot->min_amount) === -1) {
            throw new IndexerActionValidationException('Invalid minimum amount');
        }
    }
}
