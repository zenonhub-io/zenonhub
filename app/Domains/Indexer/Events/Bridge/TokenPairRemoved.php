<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Events\Bridge;

use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeNetwork;
use App\Domains\Nom\Models\Token;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TokenPairRemoved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AccountBlock $accountBlock,
        public BridgeNetwork $bridgeNetwork,
        public Token $token,
    ) {
    }
}
