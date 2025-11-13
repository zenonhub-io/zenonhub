<?php

declare(strict_types=1);

namespace App\Actions\Indexer;

use App\Services\Indexer;
use Lorisleiva\Actions\Concerns\AsAction;

class Pause
{
    use AsAction;

    public string $commandSignature = 'indexer:pause';

    public function handle(): void
    {
        $indexer = app(Indexer::class);
        $indexer->pause();
    }
}
