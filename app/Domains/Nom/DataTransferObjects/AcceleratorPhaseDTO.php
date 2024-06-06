<?php

declare(strict_types=1);

namespace App\Domains\Nom\DataTransferObjects;

use Spatie\LaravelData\Data;

class AcceleratorPhaseDTO extends Data
{
    public function __construct(
        public AcceleratorPhaseDetailsDTO $phase,
        public VoteDTO $votes,
    ) {
    }
}
