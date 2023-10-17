<?php

namespace App\Events\Nom;

use App\Models\Nom\AccountBlock;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountBlockCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public AccountBlock $block,
        public bool $sendAlerts,
    ) {
    }
}
