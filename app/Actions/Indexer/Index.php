<?php

declare(strict_types=1);

namespace App\Actions\Indexer;

use App\Services\Indexer;
use Lorisleiva\Actions\Concerns\AsAction;

class Index
{
    use AsAction;

    public string $commandSignature = 'indexer:run';

    public function handle(): void
    {
        $indexer = app(Indexer::class);
        $indexer->run();
    }
}
