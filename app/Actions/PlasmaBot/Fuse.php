<?php

namespace App\Actions\PlasmaBot;

use App;
use App\Exceptions\ApplicationException;
use App\Models\PlasmaBotEntry;
use Carbon\Carbon;
use Log;
use Spatie\QueueableAction\QueueableAction;

class Fuse
{
    use QueueableAction;

    public function __construct(
        protected string $address,
        protected int $amount,
        protected Carbon $expires
    ) {
    }

    public function execute(): bool
    {
        try {
            $plasmaBot = App::make(\App\Services\PlasmaBot::class);
            $plasmaBot->fuse($this->address, $this->amount);
        } catch (ApplicationException $exception) {
            Log::error($exception);

            return false;
        }

        PlasmaBotEntry::create([
            'address' => $this->address,
            'amount' => $this->amount,
            'expires_at' => $this->expires,
        ]);

        return true;
    }
}
