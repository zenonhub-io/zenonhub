<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Nom\SetUnwrapFromAddress;
use App\Events\Indexer\Bridge\TokenUnwraped;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeUnwrap;
use Lorisleiva\Actions\Concerns\AsAction;

class TokenUnwrapedListener
{
    use AsAction;

    /**
     * Handle the event.
     */
    public function handle(AccountBlock $accountBlock, BridgeUnwrap $unwrap): void
    {
        $this->dispatchSetUnwrapFromAddress($unwrap);
    }

    public function asListener(TokenUnwraped $tokenUnwrapedEvent): void
    {
        $this->handle($tokenUnwrapedEvent->accountBlock, $tokenUnwrapedEvent->unwrap);
    }

    private function dispatchSetUnwrapFromAddress(BridgeUnwrap $unwrap): void
    {
        SetUnwrapFromAddress::dispatch($unwrap);
    }
}
