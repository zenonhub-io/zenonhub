<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Nom;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class AcceleratorProjectDTO extends Data
{
    public function __construct(
        public string $id,
        public string $owner,
        public string $name,
        public string $description,
        public string $url,
        public string $znnFundsNeeded,
        public string $qsrFundsNeeded,
        public int $creationTimestamp,
        public int $lastUpdateTimestamp,
        public int $status,
        public ?array $phaseIds,
        public ?VoteDTO $votes,
        /** @var Collection<int, AcceleratorPhaseDTO> */
        public ?Collection $phases,
    ) {
    }
}
