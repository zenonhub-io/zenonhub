<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeNetwork;
use Illuminate\Support\Facades\Log;

class RemoveNetwork extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $this->accountBlock = $accountBlock;
        $blockData = $accountBlock->data->decoded;

        if (! validate_bridge_tx($this->block)) {
            Log::warning('Bridge action sent from non-admin');

            return;
        }

        $this->removeNetwork();

    }

    private function removeNetwork(): void
    {
        $data = $accountBlock->data->decoded;
        $bridgeNetwork = BridgeNetwork::findByNetworkChain($data['networkClass'], $data['chainId']);
        $bridgeNetwork->delete();
    }
}