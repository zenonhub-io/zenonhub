<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Enums\AccountRewardTypesEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\AccountReward;
use App\Domains\Nom\Models\BridgeUnwrap;
use App\Jobs\ProcessAccountBalance;
use Illuminate\Support\Facades\Log;
use Throwable;

class Redeem extends AbstractIndexerAction
{
    public BridgeUnwrap $unwrap;

    public function handle(AccountBlock $accountBlock): void
    {
        try {
            $this->loadUnwrap();
            $this->processRedeem();
            $this->processReward();
        } catch (Throwable $exception) {
            Log::warning('Error processing redeem ' . $this->accountBlock->hash);
            Log::debug($exception);

            return;
        }

    }

    private function loadUnwrap(): void
    {
        $data = $this->accountBlock->data->decoded;
        $this->unwrap = BridgeUnwrap::where('transaction_hash', $data['transactionHash'])
            ->where('log_index', $data['logIndex'])
            ->whereNull('redeemed_at')
            ->sole();
    }

    private function processRedeem(): void
    {
        $this->unwrap->redeemed_at = $this->accountBlock->created_at;
        $this->unwrap->save();
    }

    private function processReward(): void
    {
        if (! $this->unwrap->is_affiliate_reward) {
            return;
        }

        AccountReward::create([
            'chain_id' => $this->accountBlock->chain_id,
            'account_id' => $this->unwrap->to_account_id,
            'token_id' => $this->unwrap->token_id,
            'type' => AccountRewardTypesEnum::BRIDGE_AFFILIATE->value,
            'amount' => $this->unwrap->amount,
            'created_at' => $this->accountBlock->created_at,
        ]);

        ProcessAccountBalance::dispatch($this->unwrap->toAccount);
    }
}
