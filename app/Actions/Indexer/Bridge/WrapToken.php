<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Bridge;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Bridge\TokenWrapped;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\BridgeWrap;
use App\Models\Nom\Token;
use Illuminate\Support\Facades\Log;

class WrapToken extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $network = BridgeNetwork::findByNetworkChain($blockData['networkClass'], $blockData['chainId']);
        $token = $network?->tokens()->where('token_id', $accountBlock->token_id)->first();

        try {
            $this->validateAction($accountBlock, $network, $token);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Bridge: WrapToken failed', [
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

        if ($token->token_standard === app('znnToken')->token_standard) {
            $network->total_znn_wrapped += $wrap->amount;
            $network->total_znn_held += $wrap->amount;
            $network->save();
        }

        if ($token->token_standard === app('qsrToken')->token_standard) {
            $network->total_qsr_wrapped += $wrap->amount;
            $network->total_qsr_held += $wrap->amount;
            $network->save();
        }

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
        [$accountBlock, $bridgeNetwork, $token] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if (! $bridgeNetwork) {
            throw new IndexerActionValidationException('Invalid bridge network');
        }

        if (! $token) {
            throw new IndexerActionValidationException('Invalid token');
        }

        if (! $token->pivot->is_bridgeable) {
            throw new IndexerActionValidationException('Token is not bridgeable');
        }

        if (bccomp($accountBlock->amount, $token->pivot->min_amount) === -1) {
            throw new IndexerActionValidationException('Invalid minimum amount');
        }
    }
}
