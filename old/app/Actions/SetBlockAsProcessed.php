<?php

declare(strict_types=1);

namespace App\Actions;

use App\Domains\Nom\Models\AccountBlock;
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
