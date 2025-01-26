<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Bridge;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\TokenUnwraped;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\BridgeUnwrap;
use App\Models\Nom\Token;
use Illuminate\Support\Facades\Log;

class UnwrapToken extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $network = BridgeNetwork::findByNetworkChain($blockData['networkClass'], $blockData['chainId']);
        $token = $network?->tokens()
            ->wherePivot('token_address', $blockData['tokenAddress'])
            ->first();

        try {
            $this->validateAction($accountBlock, $network, $token);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Bridge: UnwrapToken failed', [
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

        if ($token->token_standard === NetworkTokensEnum::ZNN->value) {
            $network->total_znn_unwrapped += $unwrap->amount;
            $network->total_znn_held -= $unwrap->amount;
            $network->save();
        }

        if ($token->token_standard === NetworkTokensEnum::QSR->value) {
            $network->total_qsr_unwrapped += $unwrap->amount;
            $network->total_qsr_held -= $unwrap->amount;
            $network->save();
        }

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
        [$accountBlock, $bridgeNetwork, $token] = func_get_args();
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
