<?php

namespace App\Actions\PlasmaBot;

use App;
use App\Exceptions\ApplicationException;
use App\Models\PlasmaBotEntry;
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
            $plasmaBot = App::make(\App\Services\PlasmaBot::class);
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
