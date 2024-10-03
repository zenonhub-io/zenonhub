<?php

declare(strict_types=1);

namespace App\Events\Indexer;

use App\DataTransferObjects\Nom\AccountBlockDTO;
use App\Models\Nom\AccountBlock;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountBlockInserted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AccountBlock $accountBlock,
        public AccountBlockDTO $accountBlockDTO,
    ) {}
}
