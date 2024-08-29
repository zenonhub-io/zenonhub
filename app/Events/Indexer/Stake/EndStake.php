<?php

declare(strict_types=1);

namespace App\Events\Indexer\Stake;

use App\Models\Nom\AccountBlock;
use App\Models\Nom\Stake;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EndStake
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AccountBlock $accountBlock,
        public Stake $stake,
    ) {
    }
}
