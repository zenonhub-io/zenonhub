<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Bridge\UnwrapRedeemed;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Enums\AccountRewardTypesEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\AccountReward;
use App\Domains\Nom\Models\BridgeUnwrap;
use App\Jobs\ProcessAccountBalance;
use Illuminate\Support\Facades\Log;
use Throwable;

class Redeem extends AbstractContractMethodProcessor
{
    public BridgeUnwrap $unwrap;

    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Bridge: Redeem failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        // Logic here

        UnwrapRedeemed::dispatch($accountBlock);

        Log::info('Contract Method Processor - Bridge: Redeem complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
        ]);

        $this->setBlockAsProcessed($accountBlock);

        //        $this->accountBlock = $accountBlock;
        //        $blockData = $accountBlock->data->decoded;
        //
        //        try {
        //            $this->loadUnwrap();
        //            $this->processRedeem();
        //            $this->processReward();
        //        } catch (Throwable $exception) {
        //            Log::warning('Error processing redeem ' . $accountBlock->hash);
        //            Log::debug($exception);
        //
        //            return;
        //        }

    }

    /**
     * @throws IndexerActionValidationException
     */
    public function validateAction(): void
    {
        /**
         * @var AccountBlock $accountBlock
         */
        [$accountBlock] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        //throw new IndexerActionValidationException('');
    }

    private function loadUnwrap(): void
    {
        $data = $accountBlock->data->decoded;
        $this->unwrap = BridgeUnwrap::where('transaction_hash', $data['transactionHash'])
            ->where('log_index', $data['logIndex'])
            ->whereNull('redeemed_at')
            ->sole();
    }

    private function processRedeem(): void
    {
        $this->unwrap->redeemed_at = $accountBlock->created_at;
        $this->unwrap->save();
    }

    private function processReward(): void
    {
        if (! $this->unwrap->is_affiliate_reward) {
            return;
        }

        AccountReward::create([
            'chain_id' => $accountBlock->chain_id,
            'account_id' => $this->unwrap->to_account_id,
            'token_id' => $this->unwrap->token_id,
            'type' => AccountRewardTypesEnum::BRIDGE_AFFILIATE->value,
            'amount' => $this->unwrap->amount,
            'created_at' => $accountBlock->created_at,
        ]);

        ProcessAccountBalance::dispatch($this->unwrap->toAccount);
    }
}
