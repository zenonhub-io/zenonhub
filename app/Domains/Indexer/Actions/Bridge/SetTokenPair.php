<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Bridge\TokenPairSet;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Actions\CheckTimeChallenge;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeAdmin;
use App\Domains\Nom\Models\BridgeNetwork;
use App\Domains\Nom\Models\Token;
use Illuminate\Support\Facades\Log;

class SetTokenPair extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('momentum');
        $blockData = $accountBlock->data->decoded;
        $bridgeNetwork = BridgeNetwork::findByNetworkChain($blockData['networkClass'], $blockData['chainId']);
        $token = load_token($blockData['tokenStandard']);

        try {
            $this->validateAction($accountBlock, $bridgeNetwork, $token);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Bridge: SetTokenPair failed', [
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

        if ($blockData['owned'] && in_array($blockData['tokenStandard'], [NetworkTokensEnum::ZNN->value, NetworkTokensEnum::QSR->value], true)) {
            throw new IndexerActionValidationException('Unable to assign ZNN or QSR token standard');
        }

        //        if (! isHex($blockData['tokenAddress'])) {
        //            throw new IndexerActionValidationException('Invalid contractAddress');
        //        }

        if ($blockData['tokenStandard'] === NetworkTokensEnum::EMPTY->value) {
            throw new IndexerActionValidationException('Unable to assign empty ZTS');
        }

        if ($blockData['feePercentage'] > config('nom.bridge.maximumFee')) {
            throw new IndexerActionValidationException('Fee exceeds max fee limit');
        }

        if ($blockData['redeemDelay'] === 0) {
            throw new IndexerActionValidationException('Fee exceeds max fee limit');
        }

        $challengeHashData = json_encode($blockData);
        $timeChallenge = (new CheckTimeChallenge)
            ->handle($accountBlock, $challengeHashData, config('nom.bridge.minSoftDelay'));

        if ($timeChallenge->is_active) {
            throw new IndexerActionValidationException('Time challenge is still active');
        }
    }
}