<?php

declare(strict_types=1);

namespace App\Domains\Nom\DataTransferObjects;

use Spatie\LaravelData\Data;

class AcceleratorPhaseDetailsDTO extends Data
{
    public function __construct(
        public string $id,
        public string $projectID,
        public string $name,
        public string $description,
        public string $url,
        public string $znnFundsNeeded,
        public string $qsrFundsNeeded,
        public int $creationTimestamp,
        public int $acceptedTimestamp,
        public int $status,
    ) {
    }
}
