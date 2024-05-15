<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Liquidity;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Stake\EndStake;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Stake;

class CancelLiquidityStake extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $this->accountBlock = $accountBlock;
        $blockData = $accountBlock->data->decoded;
        $stake = Stake::findBy('hash', $blockData['id']);

        if (! $stake || ! $this->validateAction($stake)) {
            return;
        }

        $stake->ended_at = $accountBlock->created_at;
        $stake->save();

        EndStake::dispatch($accountBlock, $stake);
    }

    protected function validateAction(): bool
    {
        [$stake] = func_get_args();

        if ($stake->end_date < now()) {
            return false;
        }

        return $stake->account_id === $this->accountBlock->account_id;
    }
}
