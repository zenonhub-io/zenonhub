<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Events\Accelerator;

use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AccountBlock;
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
