<?php

declare(strict_types=1);

namespace App\Events\Indexer\Token;

use App\Models\Nom\AccountBlock;
use App\Models\Nom\TokenBurn;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TokenBurned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AccountBlock $accountBlock,
        public TokenBurn $tokenBurn,
    ) {}
}
