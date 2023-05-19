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
        protected int $amount = 10
    ) {
    }

    public function execute(): void
    {
        try {
            $plasmaBot = new PlasmaBot();
            $result = $plasmaBot->fuse($this->address, $this->amount);

            if ($result) {
                PlasmaBotEntry::create([
                    'address' => $this->address,
                    'amount' => $this->amount,
                    'expires_at' => now()->addDay(),
                ]);
            }
        } catch (ApplicationException) {

        }
    }
}
