<?php

declare(strict_types=1);

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeNetwork;
use App\Domains\Nom\Models\Chain;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

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
        if (! validate_bridge_tx($this->block)) {
            Log::warning('Bridge action sent from non-admin');

            return;
        }

        $data = $this->block->data->decoded;

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
        $bridgeNetwork->updated_at = $this->block->created_at;

        if (! $bridgeNetwork->created_at) {
            $bridgeNetwork->created_at = $this->block->created_at;
        }

        $bridgeNetwork->save();

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
