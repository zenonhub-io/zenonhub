<?php

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Classes\Utilities;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\Chain;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SetNetwork implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;

    public int $backoff = 10;

    public AccountBlock $block;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        if (! Utilities::validateBridgeTx($this->block)) {
            Log::error('Bridge action sent from non-admin');

            return;
        }

        $networkData = $this->block->data->decoded;

        try {
            $chain = Chain::where('chain_identifier', $networkData['chainId'])->sole();
        } catch (ModelNotFoundException $exception) {
            Log::error("Set bridge network error, unknown chainId: {$networkData['chainId']}");

            return;
        }

        $bridgeNetwork = BridgeNetwork::firstOrNew([
            'chain_id' => $chain->id,
            'network_class' => $networkData['networkClass'],
            'chain_identifier' => $networkData['chainId'],
        ]);

        $bridgeNetwork->name = $networkData['name'];
        $bridgeNetwork->contract_address = $networkData['contractAddress'];
        $bridgeNetwork->meta_data = json_decode($networkData['metadata']);
        $bridgeNetwork->updated_at = $this->block->created_at;

        if (! $bridgeNetwork->created_at) {
            $bridgeNetwork->created_at = $this->block->created_at;
        }

        $bridgeNetwork->save();

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
