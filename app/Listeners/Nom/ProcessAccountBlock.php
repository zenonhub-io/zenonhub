<?php

namespace App\Listeners\Nom;

use App\Actions\ProcessBlock;
use App\Events\Nom\AccountBlockCreated;

class ProcessAccountBlock
{
    public function handle(AccountBlockCreated $event): void
    {
        (new ProcessBlock(
            $event->block,
            $event->sendWhaleAlerts,
            $event->syncAccountBalances
        ))->execute();
    }
}
