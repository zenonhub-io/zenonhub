<?php

declare(strict_types=1);

namespace App\Domains\Nom\Events;

use App\Domains\Nom\DataTransferObjects\MomentumData as MomentumDTO;
use App\Domains\Nom\Models\Momentum;
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