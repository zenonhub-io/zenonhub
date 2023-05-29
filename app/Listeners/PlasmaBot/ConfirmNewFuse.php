<?php

namespace App\Listeners\PlasmaBot;

use App\Events\Nom\Plasma\Fuse;
use App\Models\PlasmaBotEntry;

class ConfirmNewFuse
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        // ...
    }

    /**
     * Handle the event.
     */
    public function handle(Fuse $event): void
    {
        if ($event->block->account->address !== config('plasma-bot.address')) {
            return;
        }

        $entry = PlasmaBotEntry::isUnConfirmed()
            ->where('address', $event->data['address'])
            ->first();

        if (! $entry) {
            return;
        }

        $entry->confirm($event->block->hash);
    }
}
