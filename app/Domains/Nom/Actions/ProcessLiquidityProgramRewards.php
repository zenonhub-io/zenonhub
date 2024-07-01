<?php

declare(strict_types=1);

namespace App\Domains\Nom\Actions;

use App\Domains\Nom\Enums\AccountRewardTypesEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\AccountReward;
use Lorisleiva\Actions\Concerns\AsAction;

class ProcessLiquidityProgramRewards
{
    use AsAction;

    public function handle(AccountBlock $accountBlock): void
    {
        AccountReward::create([
            'chain_id' => $accountBlock->chain->id,
            'account_id' => $accountBlock->toAccount->id,
            'token_id' => $accountBlock->token->id,
            'type' => AccountRewardTypesEnum::LIQUIDITY_PROGRAM->value,
            'amount' => $accountBlock->amount,
            'created_at' => $accountBlock->created_at,
        ]);
    }
}
