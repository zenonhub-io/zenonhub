<?php

declare(strict_types=1);

namespace App\Actions\PlasmaBot;

use App\Models\PlasmaBotEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
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
        $plasmaBot = App::make(\App\Domains\Nom\Services\PlasmaBot::class);
        $result = $plasmaBot->fuse($this->address, $this->amount);

        if (! $result) {
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
