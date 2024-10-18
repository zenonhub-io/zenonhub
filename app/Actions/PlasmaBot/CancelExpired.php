<?php

declare(strict_types=1);

namespace App\Actions\PlasmaBot;

use App\Models\PlasmaBotEntry;
use Lorisleiva\Actions\Concerns\AsAction;

class CancelExpired
{
    use AsAction;

    public function handle(): void
    {
        $expiredEntries = PlasmaBotEntry::whereExpired()->whereConfirmed()->get();
        $expiredEntries->each(function ($entry) {
            Cancel::run($entry->hash);
        });
    }
}
