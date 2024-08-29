<?php

declare(strict_types=1);

namespace App\Events\Indexer\Plasma;

use App\Models\Nom\AccountBlock;
use App\Models\Nom\Plasma;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EndFuse
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AccountBlock $accountBlock,
        public Plasma $plasma,
    ) {
    }
}
