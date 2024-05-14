<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Events\Token;

use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Token;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TokenUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AccountBlock $accountBlock,
        public Token $token,
    ) {
    }
}
