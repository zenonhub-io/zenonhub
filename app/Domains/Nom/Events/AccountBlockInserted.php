<?php

declare(strict_types=1);

namespace App\Domains\Nom\Events;

use App\Domains\Nom\DataTransferObjects\AccountBlockDTO;
use App\Domains\Nom\Models\AccountBlock;
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
    ) {
    }
}
