<?php

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Classes\Utilities;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\BridgeNetworkToken;
use App\Models\Nom\Token;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        if (! Utilities::validateBridgeTx($this->block)) {
            Log::error('Bridge action sent from non-admin');

            return;
        }

        try {
            $data = $this->block->data->decoded;
            $token = Token::findByZts($data['tokenStandard']);
            $network = BridgeNetwork::findByNetworkChain($data['networkClass'], $data['chainId']);

            $networkToken = BridgeNetworkToken::where('bridge_network_id', $network->id)
                ->where('token_id', $token->id)
                ->where('token_address', $data['tokenAddress'])
                ->sole();
        } catch (ModelNotFoundException $exception) {
            Log::error('Remove token pair error');

            return;
        }

        $networkToken->delete();

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
