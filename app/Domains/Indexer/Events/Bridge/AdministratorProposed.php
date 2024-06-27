<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Events\Bridge;

use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeAdmin;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdministratorProposed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AccountBlock $accountBlock,
        public BridgeAdmin $bridgeAdmin,
    ) {
    }
}
