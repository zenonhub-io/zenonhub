<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Nom\ProcessBridgeUnwrapReward;
use App\Events\Indexer\Bridge\UnwrapRedeemed;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeUnwrap;
use Lorisleiva\Actions\Concerns\AsAction;

class UnwrapRedeemedListener
{
    use AsAction;

    /**
     * Handle the event.
     */
    public function handle(AccountBlock $accountBlock, BridgeUnwrap $unwrap): void
    {
        $this->dispatchUnwrapRewardProcessor($accountBlock, $unwrap);
    }

    public function asListener(UnwrapRedeemed $unwrapRedeemedEvent): void
    {
        $this->handle($unwrapRedeemedEvent->accountBlock, $unwrapRedeemedEvent->unwrap);
    }

    private function dispatchUnwrapRewardProcessor(AccountBlock $accountBlock, BridgeUnwrap $unwrap): void
    {
        if (! $unwrap->is_affiliate_reward) {
            return;
        }

        ProcessBridgeUnwrapReward::run($accountBlock, $unwrap);
    }
}
