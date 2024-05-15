<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeNetwork;
use Illuminate\Support\Facades\Log;
use Throwable;

class SetTokenPair extends AbstractContractMethodProcessor
{
    public BridgeNetwork $network;

    public array $blockData;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->blockData = $accountBlock->data->decoded;
        $this->onQueue('indexer');
    }

    public function handle(AccountBlock $accountBlock): void
    {
        $this->accountBlock = $accountBlock;
        $blockData = $accountBlock->data->decoded;

        if (! validate_bridge_tx($this->block)) {
            Log::warning('Bridge action sent from non-admin');

            return;
        }

        try {
            $this->loadNetwork();
            $this->setTokenPair();
        } catch (Throwable $exception) {
            Log::warning('Unable to set token pair: ' . $accountBlock->hash);
            Log::debug($exception);

            return;
        }

    }

    private function loadNetwork(): void
    {
        $this->network = BridgeNetwork::findByNetworkChain($this->blockData['networkClass'], $this->blockData['chainId']);
    }

    private function setTokenPair(): void
    {
        $token = load_token($this->blockData['tokenStandard']);
        $this->network->tokens()->updateOrCreate([
            'token_id' => $token->id,
        ], [
            'token_address' => $this->blockData['tokenAddress'],
            'min_amount' => $this->blockData['minAmount'],
            'fee_percentage' => $this->blockData['feePercentage'],
            'redeem_delay' => $this->blockData['redeemDelay'],
            'metadata' => json_decode($this->blockData['metadata']),
            'is_bridgeable' => $this->blockData['bridgeable'],
            'is_redeemable' => $this->blockData['redeemable'],
            'is_owned' => $this->blockData['owned'],
            'created_at' => $accountBlock->created_at,
        ]);
    }
}
