<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Nom;

use Spatie\LaravelData\Data;

class AcceleratorPhaseDTO extends Data
{
    public function __construct(
        public AcceleratorPhaseDetailsDTO $phase,
        public VoteDTO $votes,
    ) {
    }
}
