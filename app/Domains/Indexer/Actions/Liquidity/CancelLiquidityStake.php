<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Liquidity;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Stake;

class CancelLiquidityStake extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $this->accountBlock->data->decoded;

        $stake = Stake::where('hash', $blockData['id'])->first();

        if ($stake) {
            $stake->ended_at = $this->accountBlock->created_at;
            $stake->save();
        }

    }
}
