<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeNetwork;
use App\Domains\Nom\Models\Chain;
use Illuminate\Support\Facades\Log;
use Throwable;

class SetNetwork extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        if (! validate_bridge_tx($this->block)) {
            Log::warning('Bridge action sent from non-admin');

            return;
        }

        $data = $accountBlock->data->decoded;

        try {
            $chain = Chain::where('chain_identifier', $data['chainId'])->sole();
        } catch (Throwable $exception) {
            Log::warning("Set bridge network error, unknown chainId: {$data['chainId']}");
            Log::debug($exception);

            return;
        }

        $bridgeNetwork = BridgeNetwork::firstOrNew([
            'chain_id' => $chain->id,
            'network_class' => $data['networkClass'],
            'chain_identifier' => $data['chainId'],
        ]);

        $bridgeNetwork->name = $data['name'];
        $bridgeNetwork->contract_address = $data['contractAddress'];
        $bridgeNetwork->meta_data = json_decode($data['metadata']);
        $bridgeNetwork->updated_at = $accountBlock->created_at;

        if (! $bridgeNetwork->created_at) {
            $bridgeNetwork->created_at = $accountBlock->created_at;
        }

        $bridgeNetwork->save();

    }
}
