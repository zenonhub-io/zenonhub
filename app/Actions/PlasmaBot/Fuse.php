<?php

namespace App\Actions\PlasmaBot;

use App;
use App\Exceptions\ApplicationException;
use App\Models\PlasmaBotEntry;
use Spatie\QueueableAction\QueueableAction;

class Fuse
{
    use QueueableAction;

    public function __construct(
        protected string $address,
        protected int $amount = 10
    ) {
    }

    public function execute(): bool
    {
        try {
            $plasmaBot = App::make(\App\Services\PlasmaBot::class);
            $result = $plasmaBot->fuse($this->address, $this->amount);

            if ($result) {
                PlasmaBotEntry::create([
                    'address' => $this->address,
                    'amount' => $this->amount,
                    'expires_at' => now()->addDay(),
                ]);

                return true;
            }
        } catch (ApplicationException) {
            return false;
        }
    }
}
