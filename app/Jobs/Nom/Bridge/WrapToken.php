<?php

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\BridgeWrap;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WrapToken implements ShouldQueue
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
            $this->processWrap();
        } catch (\Throwable $exception) {
            Log::warning('Unable to process wrap: '.$this->block->hash);
            Log::debug($exception);

            return;
        }

        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function processWrap(): void
    {
        $data = $this->block->data->decoded;
        $network = BridgeNetwork::findByNetworkChain($data['networkClass'], $data['chainId']);

        BridgeWrap::create([
            'bridge_network_id' => $network->id,
            'account_id' => $this->block->account_id,
            'token_id' => $this->block->token_id,
            'account_block_id' => $this->block->id,
            'to_address' => $data['toAddress'],
            'amount' => $this->block->amount,
            'created_at' => $this->block->created_at,
        ]);
    }
}
