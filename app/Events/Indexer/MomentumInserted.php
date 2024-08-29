<?php

declare(strict_types=1);

namespace App\Events\Indexer;

use App\DataTransferObjects\Nom\MomentumDTO;
use App\Models\Nom\Momentum;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MomentumInserted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Momentum $momentum,
        public MomentumDTO $momentumDTO,
    ) {
    }
}
