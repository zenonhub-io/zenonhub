<?php

declare(strict_types=1);

namespace App\Events\Indexer\Accelerator;

use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AccountBlock;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PhaseCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AccountBlock $accountBlock,
        public AcceleratorPhase $acceleratorPhase,
    ) {
    }
}
