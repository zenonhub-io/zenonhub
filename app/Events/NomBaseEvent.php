<?php

namespace App\Events;

use App\Models\Nom\AccountBlock;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class NomBaseEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public AccountBlock $block;

    /**
     * @var mixed Decoded block data
     */
    public mixed $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($block, $data)
    {
        $this->block = $block;
        $this->data = $data;
    }
}
