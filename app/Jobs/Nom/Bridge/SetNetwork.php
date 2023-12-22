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

        $data = $this->block->data->decoded;

        try {
            $chain = Chain::where('chain_identifier', $data['chainId'])->sole();
        } catch (\Throwable $exception) {
            Log::error("Set bridge network error, unknown chainId: {$data['chainId']}");
            Log::error($exception->getMessage());

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
        $bridgeNetwork->updated_at = $this->block->created_at;

        if (! $bridgeNetwork->created_at) {
            $bridgeNetwork->created_at = $this->block->created_at;
        }

        $bridgeNetwork->save();

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
