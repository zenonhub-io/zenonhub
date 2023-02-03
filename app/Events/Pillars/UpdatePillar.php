<?php

namespace App\Events\Pillars;

use App\Models\Nom\AccountBlock;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdatePillar
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var AccountBlock
     */
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
