<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeNetwork;
use Illuminate\Support\Facades\Log;

class RemoveNetwork extends AbstractIndexerAction
{
    public function handle(AccountBlock $accountBlock): void
    {
        if (! validate_bridge_tx($this->block)) {
            Log::warning('Bridge action sent from non-admin');

            return;
        }

        $this->removeNetwork();

    }

    private function removeNetwork(): void
    {
        $data = $this->accountBlock->data->decoded;
        $bridgeNetwork = BridgeNetwork::findByNetworkChain($data['networkClass'], $data['chainId']);
        $bridgeNetwork->delete();
    }
}
