<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\PlasmaBot\ConfirmEntry;
use App\Events\Indexer\Plasma\StartFuse;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Plasma;
use Lorisleiva\Actions\Concerns\AsAction;

class StartFuseListener
{
    use AsAction;

    /**
     * Handle the event.
     */
    public function handle(AccountBlock $accountBlock, Plasma $plasma): void
    {
        $this->dispatchConfirmPlasmaBotEntry($accountBlock, $plasma);
    }

    public function asListener(StartFuse $startFuseEvent): void
    {
        $this->handle($startFuseEvent->accountBlock, $startFuseEvent->plasma);
    }

    private function dispatchConfirmPlasmaBotEntry(AccountBlock $accountBlock, Plasma $plasma): void
    {
        if ($accountBlock->account->address !== config('services.plasma-bot.address')) {
            return;
        }

        ConfirmEntry::dispatch($plasma);
    }
}
