<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Liquidity;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Stake\StartStake;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Stake as StakeModel;
use Illuminate\Support\Facades\Log;

class LiquidityStake extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        if (! $this->validateAction($accountBlock)) {
            Log::info('Contract Method Processor - Liquidity: LiquidityStake failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        $stake = StakeModel::create([
            'chain_id' => $accountBlock->chain_id,
            'account_id' => $accountBlock->account_id,
            'token_id' => $accountBlock->token_id,
            'amount' => $accountBlock->amount,
            'duration' => $blockData['durationInSec'],
            'hash' => $accountBlock->hash,
            'started_at' => $accountBlock->created_at,
        ]);

        StartStake::dispatch($accountBlock, $stake);

        Log::info('Contract Method Processor - Liquidity: LiquidityStake complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'stake' => $stake,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    protected function validateAction(): bool
    {
        /**
         * @var AccountBlock $accountBlock
         */
        [$accountBlock] = func_get_args();

        if ($accountBlock->token->token_standard !== NetworkTokensEnum::LP_ZNN_ETH->value) {
            return false;
        }

        if ($accountBlock->amount <= 0) {
            return false;
        }

        if ($accountBlock->data->decoded['durationInSec'] <= 0) {
            return false;
        }

        return true;
    }
}
