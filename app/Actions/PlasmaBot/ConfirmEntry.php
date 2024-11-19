<?php

declare(strict_types=1);

namespace App\Actions\PlasmaBot;

use App\Models\Nom\Plasma;
use App\Models\PlasmaBotEntry;
use Lorisleiva\Actions\Concerns\AsAction;

class ConfirmEntry
{
    use AsAction;

    public function handle(Plasma $plasma): void
    {
        $plasma->load('toAccount', 'accountBlock');

        $entry = PlasmaBotEntry::whereUnConfirmed()
            ->where('address', $plasma->toAccount->address)
            ->first();

        if (! $entry) {
            return;
        }

        $entry->hash = $plasma->accountBlock->hash;
        $entry->is_confirmed = true;
        $entry->save();
    }
}
