<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeNetwork;
use Illuminate\Support\Facades\Log;

class SetNetworkMetadata extends AbstractIndexerAction
{
    public function handle(AccountBlock $accountBlock): void
    {
        if (! validate_bridge_tx($this->block)) {
            Log::warning('Bridge action sent from non-admin');

            return;
        }

        $data = $this->accountBlock->data->decoded;
        $bridgeNetwork = BridgeNetwork::findByNetworkChain($data['networkClass'], $data['chainId']);

        $bridgeNetwork->meta_data = json_decode($data['metadata']);
        $bridgeNetwork->save();

    }
}
