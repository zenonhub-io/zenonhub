<?php

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\AccountReward;
use App\Models\Nom\BridgeUnwrap;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Redeem implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;

    public int $backoff = 10;

    public AccountBlock $block;

    public BridgeUnwrap $unwrap;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        try {
            $this->loadUnwrap();
            $this->processRedeem();
            $this->processReward();
        } catch (\Throwable $exception) {
            Log::error('Error processing redeem '.$this->block->hash);
            Log::error($exception->getMessage());

            return;
        }

        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function loadUnwrap(): void
    {
        $data = $this->block->data->decoded;
        $this->unwrap = BridgeUnwrap::where('transaction_hash', $data['transactionHash'])
            ->where('log_index', $data['logIndex'])
            ->sole();
    }

    private function processRedeem(): void
    {
        $this->unwrap->redeemed_at = $this->block->created_at;
        $this->unwrap->save();
    }

    private function processReward(): void
    {
        if (! $this->unwrap->is_affiliate_reward) {
            return;
        }

        AccountReward::create([
            'chain_id' => $this->block->chain_id,
            'account_id' => $this->unwrap->to_account_id,
            'token_id' => $this->unwrap->token_id,
            'type' => AccountReward::TYPE_BRIDGE_AFFILIATE,
            'amount' => $this->unwrap->amount,
            'created_at' => $this->block->created_at,
        ]);
    }
}
