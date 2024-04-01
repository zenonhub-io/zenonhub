<?php

namespace App\Actions\PlasmaBot;

use App\Models\PlasmaBotEntry;
use App\Services\PlasmaBot;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Spatie\QueueableAction\QueueableAction;

class Fuse
{
    use QueueableAction;

    public function execute(
        string $address,
        int $amount,
        ?Carbon $expires
    ): bool {
        $plasmaBot = App::make(PlasmaBot::class);
        $result = $plasmaBot->fuse($address, $amount);

        if (! $result) {
            return false;
        }

        PlasmaBotEntry::create([
            'address' => $address,
            'amount' => $amount,
            'expires_at' => $expires,
        ]);

        return true;
    }
}
