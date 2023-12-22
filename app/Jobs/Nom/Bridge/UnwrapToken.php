<?php

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Classes\Utilities;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\BridgeNetworkToken;
use App\Models\Nom\BridgeUnwrap;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UnwrapToken implements ShouldQueue
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
        try {
            $this->processUnwrap();
        } catch (\Throwable $throwable) {
            Log::error('Unable to process unwrap: '.$this->block->hash);
            Log::error($throwable->getMessage());

            return;
        }

        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function processUnwrap(): void
    {
        $data = $this->block->data->decoded;
        $network = BridgeNetwork::findByNetworkChain($data['networkClass'], $data['chainId']);
        $account = Utilities::loadAccount($data['toAddress']);
        $bridgeToken = BridgeNetworkToken::findByTokenAddress($network->id, $data['tokenAddress']);

        $unwrap = BridgeUnwrap::updateOrCreate([
            'transaction_hash' => $data['transactionHash'],
            'log_index' => $data['logIndex'],
        ], [
            'bridge_network_id' => $network->id,
            'bridge_network_token_id' => $bridgeToken->id,
            'to_account_id' => $account->id,
            'token_id' => $bridgeToken->token->id,
            'account_block_id' => $this->block->id,
            'signature' => $data['signature'],
            'amount' => $data['amount'],
            'created_at' => $this->block->created_at,
        ]);

        $unwrap->setFromAddress();
    }
}
