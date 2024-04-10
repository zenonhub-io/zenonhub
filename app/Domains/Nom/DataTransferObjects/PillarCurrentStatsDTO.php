<?php

declare(strict_types=1);

namespace App\Domains\Nom\DataTransferObjects;

use Spatie\LaravelData\Data;

class PillarCurrentStatsDTO extends Data
{
    public function __construct(
        public int $producedMomentums,
        public int $expectedMomentums,
    ) {
    }
}
