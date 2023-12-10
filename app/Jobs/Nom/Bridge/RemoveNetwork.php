<?php

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Classes\Utilities;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RemoveNetwork implements ShouldQueue
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
            $bridgeNetwork = BridgeNetwork::where([
                'network_class' => $networkData['networkClass'],
                'chain_identifier' => $networkData['chainId'],
            ])->sole();
        } catch (ModelNotFoundException $exception) {
            Log::error('Remove bridge network error, unknown network', $networkData);

            return;
        }

        $bridgeNetwork->delete();

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
