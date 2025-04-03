<?php

declare(strict_types=1);

namespace App\Actions\Sync;

use Lorisleiva\Actions\Concerns\AsAction;

class PillarsApr
{
    use AsAction;

    public string $commandSignature = 'sync:pillars-apr';

    public function handle(): void
    {
        // The construct updates each individual Pillars APR data
        new \App\Services\AprData\PillarsApr;
    }
}
