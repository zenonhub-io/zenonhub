<?php

declare(strict_types=1);

namespace App\Events\Indexer\Pillar;

use App\Models\Nom\AccountBlock;
use App\Models\Nom\Pillar;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PillarUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AccountBlock $accountBlock,
        public Pillar $pillar,
    ) {}
}
