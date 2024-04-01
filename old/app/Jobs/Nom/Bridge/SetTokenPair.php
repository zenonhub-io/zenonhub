<?php

declare(strict_types=1);

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeNetwork;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SetTokenPair implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;

    public int $backoff = 10;

    public AccountBlock $block;

    public BridgeNetwork $network;

    public array $blockData;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->blockData = $this->block->data->decoded;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        if (! validate_bridge_tx($this->block)) {
            Log::warning('Bridge action sent from non-admin');

            return;
        }

        try {
            $this->loadNetwork();
            $this->setTokenPair();
        } catch (Throwable $exception) {
            Log::warning('Unable to set token pair: ' . $this->block->hash);
            Log::debug($exception);

            return;
        }

        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function loadNetwork(): void
    {
        $this->network = BridgeNetwork::findByNetworkChain($this->blockData['networkClass'], $this->blockData['chainId']);
    }

    private function setTokenPair(): void
    {
        $token = load_token($this->blockData['tokenStandard']);
        $this->network->tokens()->updateOrCreate([
            'token_id' => $token->id,
        ], [
            'token_address' => $this->blockData['tokenAddress'],
            'min_amount' => $this->blockData['minAmount'],
            'fee_percentage' => $this->blockData['feePercentage'],
            'redeem_delay' => $this->blockData['redeemDelay'],
            'metadata' => json_decode($this->blockData['metadata']),
            'is_bridgeable' => $this->blockData['bridgeable'],
            'is_redeemable' => $this->blockData['redeemable'],
            'is_owned' => $this->blockData['owned'],
            'created_at' => $this->block->created_at,
        ]);
    }
}
