<?php

namespace App\Actions\PlasmaBot;

use App\Exceptions\ApplicationException;
use App\Models\PlasmaBotEntry;
use App\Services\PlasmaBot;
use Spatie\QueueableAction\QueueableAction;

class Fuse
{
    use QueueableAction;

    public function __construct(
        protected string $address,
    ) {
    }

    public function execute(): void
    {
        $amount = 10;
        $plasmaBot = new PlasmaBot();

        try {
            $plasmaBot->fuse($this->address, $amount);
            PlasmaBotEntry::create([
                'address' => $this->address,
                'amount' => $amount,
                'expires_at' => now()->addDay(),
            ]);
        } catch (ApplicationException) {

        }
    }
}
