<?php

declare(strict_types=1);

namespace App\Events\Nom;

use App\Domains\Nom\Models\AccountBlock;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class NomBaseEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public AccountBlock $block,
        public mixed $data
    ) {
    }
}
