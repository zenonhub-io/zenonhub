<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use App\Enums\Nom\AccountRewardTypesEnum;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\AccountReward;
use App\Models\Nom\BridgeUnwrap;
use Lorisleiva\Actions\Concerns\AsAction;

class ProcessBridgeUnwrapReward
{
    use AsAction;

    public function handle(AccountBlock $accountBlock, BridgeUnwrap $unwrap): void
    {
        if (! $unwrap->is_affiliate_reward) {
            return;
        }

        AccountReward::create([
            'chain_id' => $accountBlock->chain_id,
            'account_id' => $unwrap->to_account_id,
            'token_id' => $unwrap->token_id,
            'type' => AccountRewardTypesEnum::BRIDGE_AFFILIATE->value,
            'amount' => $unwrap->amount,
            'created_at' => $accountBlock->created_at,
        ]);
    }
}
