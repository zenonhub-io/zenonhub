<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeNetwork;
use App\Domains\Nom\Models\BridgeNetworkToken;
use Illuminate\Support\Facades\Log;
use Throwable;

class RemoveTokenPair extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        if (! validate_bridge_tx($this->block)) {
            Log::warning('Bridge action sent from non-admin');

            return;
        }

        try {
            $this->removeTokenPair();
        } catch (Throwable $exception) {
            Log::warning('Remove token pair error ' . $accountBlock->hash);
            Log::debug($exception);

            return;
        }

    }

    private function removeTokenPair(): void
    {
        $data = $accountBlock->data->decoded;
        $network = BridgeNetwork::findByNetworkChain($data['networkClass'], $data['chainId']);
        $networkToken = BridgeNetworkToken::findByTokenAddress($network->id, $data['tokenAddress']);
        $networkToken->delete();
    }
}
