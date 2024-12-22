<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Nom\ProcessBlockRewards;
use App\Events\Indexer\Token\TokenMinted;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\TokenMint;
use Lorisleiva\Actions\Concerns\AsAction;

class TokenMintedListener
{
    use AsAction;

    /**
     * Handle the event.
     */
    public function handle(AccountBlock $accountBlock, TokenMint $tokenMint): void
    {
        $this->dispatchBlockRewardProcessor($tokenMint);
    }

    public function asListener(TokenMinted $tokenMintedEvent): void
    {
        $this->handle($tokenMintedEvent->accountBlock, $tokenMintedEvent->tokenMint);
    }

    private function dispatchBlockRewardProcessor(TokenMint $tokenMint): void
    {
        //        if (! $accountBlock->parent || $accountBlock->parent->pairedAccountBlock->contractMethod->name !== 'CollectReward') {
        //            return;
        //        }

        ProcessBlockRewards::dispatch($tokenMint);
    }
}
