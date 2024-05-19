<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Stake;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Stake\StartStake;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Stake as StakeModel;
use Illuminate\Support\Facades\Log;

class Stake extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('token');
        $blockData = $accountBlock->data->decoded;

        if (! $this->validateAction($accountBlock)) {
            Log::info('Contract Method Processor - Stake: Stake failed', [
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

        Log::info('Contract Method Processor - Stake: Stake complete', [
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
        $blockData = $accountBlock->data->decoded;

        if ($accountBlock->token->token_standard !== NetworkTokensEnum::ZNN->value) {
            return false;
        }

        if ($accountBlock->amount < config('nom.stake.minAmount')) {
            return false;
        }

        if (
            $blockData['durationInSec'] < config('nom.stake.timeMinSec') ||
            $blockData['durationInSec'] > config('nom.stake.timeMaxSec')
        ) {
            return false;
        }

        return true;
    }
}
