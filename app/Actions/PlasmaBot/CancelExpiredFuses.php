<?php

namespace App\Actions\PlasmaBot;

use App\Exceptions\ApplicationException;
use App\Models\PlasmaBotEntry;
use App\Services\PlasmaBot;
use Log;
use Spatie\QueueableAction\QueueableAction;

class CancelExpiredFuses
{
    use QueueableAction;

    public function __construct(
    ) {
    }

    public function execute(): void
    {
        try {
            $plasmaBot = new PlasmaBot();
            $expiredEntries = PlasmaBotEntry::isExpired()->get();
            $expiredEntries->each(function ($entry) use ($plasmaBot) {
                if ($plasmaBot->cancel($entry->hash)) {
                    $entry->delete();
                }
            });
        } catch (ApplicationException $exception) {
            Log::error($exception);
        }
    }
}
