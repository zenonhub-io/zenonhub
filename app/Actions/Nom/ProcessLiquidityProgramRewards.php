<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use App\Enums\Nom\AccountRewardTypesEnum;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\AccountReward;
use Lorisleiva\Actions\Concerns\AsAction;

class ProcessLiquidityProgramRewards
{
    use AsAction;

    public function handle(AccountBlock $accountBlock): void
    {
        AccountReward::create([
            'chain_id' => $accountBlock->chain_id,
            'account_block_id' => $accountBlock->id,
            'account_id' => $accountBlock->toAccount->id,
            'token_id' => $accountBlock->token->id,
            'type' => AccountRewardTypesEnum::LIQUIDITY_PROGRAM->value,
            'amount' => $accountBlock->amount,
            'created_at' => $accountBlock->created_at,
        ]);
    }
}
