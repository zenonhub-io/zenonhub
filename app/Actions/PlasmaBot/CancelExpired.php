<?php

namespace App\Actions\PlasmaBot;

use App;
use App\Models\PlasmaBotEntry;
use Spatie\QueueableAction\QueueableAction;

class CancelExpired
{
    use QueueableAction;

    public function execute(): void
    {
        $plasmaBot = App::make(\App\Services\PlasmaBot::class);
        $expiredEntries = PlasmaBotEntry::isExpired()->get();
        $expiredEntries->each(function ($entry) use ($plasmaBot) {
            if ($plasmaBot->cancel($entry->hash)) {
                $entry->delete();
            }
        });
    }
}
