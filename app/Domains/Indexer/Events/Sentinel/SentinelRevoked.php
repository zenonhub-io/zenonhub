<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Events\Sentinel;

use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Sentinel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SentinelRevoked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AccountBlock $accountBlock,
        public Sentinel $sentinel,
    ) {
    }
}
