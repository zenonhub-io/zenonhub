<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Events\Bridge;

use App\Domains\Nom\Models\AccountBlock;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class GuardiansNominated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AccountBlock $accountBlock,
        public Collection $guardians,
    ) {
    }
}
