<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Bridge;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Actions\Nom\CheckTimeChallenge;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\TokenPairSet;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeAdmin;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\Token;
use Illuminate\Support\Facades\Log;

class SetTokenPair extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('momentum');
        $blockData = $accountBlock->data->decoded;
        $bridgeNetwork = BridgeNetwork::findByNetworkChain($blockData['networkClass'], $blockData['chainId']);
        $token = Token::firstWhere('token_standard', $blockData['tokenStandard']);

        try {
            $this->validateAction($accountBlock, $bridgeNetwork, $token);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Bridge: SetTokenPair failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $bridgeNetwork->tokens()->syncWithoutDetaching([
            $token->id => [
                'token_address' => $blockData['tokenAddress'],
                'min_amount' => $blockData['minAmount'],
                'fee_percentage' => $blockData['feePercentage'],
                'redeem_delay' => $blockData['redeemDelay'],
                'metadata' => json_decode($blockData['metadata']),
                'is_bridgeable' => $blockData['bridgeable'],
                'is_redeemable' => $blockData['redeemable'],
                'is_owned' => $blockData['owned'],
                'created_at' => $accountBlock->created_at,
                'updated_at' => $accountBlock->created_at,
            ],
        ]);

        TokenPairSet::dispatch($accountBlock, $bridgeNetwork, $token);

        Log::info('Contract Method Processor - Bridge: SetTokenPair complete', [
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

        $bridgeAdmin = BridgeAdmin::getActiveAdmin();

        if ($bridgeAdmin->account_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Action sent from non admin');
        }

        if (! $bridgeNetwork) {
            throw new IndexerActionValidationException('Invalid bridgeNetwork');
        }

        if (! $token) {
            throw new IndexerActionValidationException('Invalid token');
        }

        if ($blockData['owned'] && in_array($blockData['tokenStandard'], [app('znnToken')->token_standard, app('qsrToken')->token_standard], true)) {
            throw new IndexerActionValidationException('Unable to assign ZNN or QSR token standard');
        }

        //        if (! isHex($blockData['tokenAddress'])) {
        //            throw new IndexerActionValidationException('Invalid contractAddress');
        //        }

        if ($blockData['tokenStandard'] === NetworkTokensEnum::EMPTY->zts()) {
            throw new IndexerActionValidationException('Unable to assign empty ZTS');
        }

        if ($blockData['feePercentage'] > config('nom.bridge.maximumFee')) {
            throw new IndexerActionValidationException('Fee exceeds max fee limit');
        }

        if ($blockData['redeemDelay'] === 0) {
            throw new IndexerActionValidationException('Fee exceeds max fee limit');
        }

        $challengeHashData = json_encode($blockData);
        $timeChallenge = CheckTimeChallenge::run($accountBlock, $challengeHashData, config('nom.bridge.minSoftDelay'));

        if ($timeChallenge->is_active) {
            throw new IndexerActionValidationException('Time challenge is still active');
        }
    }
}
