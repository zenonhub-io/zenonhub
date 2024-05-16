<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Stake;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Stake\StartStake;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Stake as StakeModel;

class Stake extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $this->accountBlock = $accountBlock;
        $blockData = $accountBlock->data->decoded;

        if (! $this->validateAction()) {
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
    }

    protected function validateAction(): bool
    {
        if ($this->accountBlock->token->token_standard !== NetworkTokensEnum::ZNN->value) {
            return false;
        }

        if ($this->accountBlock->amount < config('nom.stake.minAmount')) {
            return false;
        }

        if (
            $this->accountBlock->data->decoded['durationInSec'] < config('nom.stake.timeMinSec') ||
            $this->accountBlock->data->decoded['durationInSec'] > config('nom.stake.timeMaxSec')
        ) {
            return false;
        }

        return true;
    }
}
