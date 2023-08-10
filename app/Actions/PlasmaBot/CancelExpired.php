<?php

namespace App\Actions\PlasmaBot;

use App\Models\PlasmaBotEntry;
use Illuminate\Support\Facades\App;
use Spatie\QueueableAction\QueueableAction;

class CancelExpired
{
    use QueueableAction;

    public function execute(): void
    {
        $plasmaBot = App::make(\App\Services\PlasmaBot::class);
        $expiredEntries = PlasmaBotEntry::isExpired()->isConfirmed()->get();
        $expiredEntries->each(function ($entry) use ($plasmaBot) {
            if ($plasmaBot->cancel($entry->hash)) {
                $entry->delete();
            }
        });
    }
}
