<?php

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Classes\Utilities;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\Token;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SetTokenPair implements ShouldQueue
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
        $token = Token::findByZts($data['tokenStandard']);
        $network = BridgeNetwork::findByNetworkChain($data['networkClass'], $data['chainId']);

        $network->tokens()->updateOrCreate([
            'token_id' => $token->id,
        ], [
            'token_address' => $data['tokenAddress'],
            'min_amount' => $data['minAmount'],
            'fee_percentage' => $data['feePercentage'],
            'redeem_delay' => $data['redeemDelay'],
            'metadata' => json_decode($data['metadata']),
            'is_bridgeable' => $data['bridgeable'],
            'is_redeemable' => $data['redeemable'],
            'is_owned' => $data['owned'],
            'created_at' => $this->block->created_at,
        ]);

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
