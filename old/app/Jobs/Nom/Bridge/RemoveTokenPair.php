<?php

declare(strict_types=1);

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeNetwork;
use App\Domains\Nom\Models\BridgeNetworkToken;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class RemoveTokenPair implements ShouldQueue
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

        try {
            $this->removeTokenPair();
        } catch (Throwable $exception) {
            Log::warning('Remove token pair error ' . $this->block->hash);
            Log::debug($exception);

            return;
        }

        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function removeTokenPair(): void
    {
        $data = $this->block->data->decoded;
        $network = BridgeNetwork::findByNetworkChain($data['networkClass'], $data['chainId']);
        $networkToken = BridgeNetworkToken::findByTokenAddress($network->id, $data['tokenAddress']);
        $networkToken->delete();
    }
}
