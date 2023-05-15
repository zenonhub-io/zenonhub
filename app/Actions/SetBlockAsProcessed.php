<?php

namespace App\Actions;

use App\Models\Nom\AccountBlock;
use Spatie\QueueableAction\QueueableAction;

class SetBlockAsProcessed
{
    use QueueableAction;

    public function __construct(
        protected AccountBlock $block
    ) {
    }

    public function execute(): void
    {
        if ($this->block->data) {
            $this->block->data->is_processed = true;
            $this->block->data->save();
        }
    }
}
