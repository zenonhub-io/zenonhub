<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Events\Accelerator;

use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PillarVoted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AccountBlock $accountBlock,
        public Pillar $pillar,
        public AcceleratorProject|AcceleratorPhase $acceleratorItem,
    ) {
    }
}
