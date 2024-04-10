<?php

declare(strict_types=1);

namespace App\Domains\Nom\Listeners;

use App\Domains\Nom\Events\AccountBlockInserted;

class UpdateAccountTotals
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AccountBlockInserted $event): void
    {
        $accountBlock = $event->accountBlock;

    }
}
