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

        $tokenData = $this->block->data->decoded;
        $token = Token::findByZts($tokenData['tokenStandard']);
        $network = BridgeNetwork::where('chain_identifier', $tokenData['chainId'])
            ->where('network_class', $tokenData['networkClass'])
            ->first();

        $network->tokens()->updateOrCreate([
            'token_id' => $token->id,
        ], [
            'token_address' => $tokenData['tokenAddress'],
            'min_amount' => $tokenData['minAmount'],
            'fee_percentage' => $tokenData['feePercentage'],
            'redeem_delay' => $tokenData['redeemDelay'],
            'metadata' => json_decode($tokenData['metadata']),
            'is_bridgeable' => $tokenData['bridgeable'],
            'is_redeemable' => $tokenData['redeemable'],
            'is_owned' => $tokenData['owned'],
            'created_at' => $this->block->created_at,
        ]);

        (new SetBlockAsProcessed($this->block))->execute();
    }
}
