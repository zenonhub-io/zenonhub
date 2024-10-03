<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Nom;

use Spatie\LaravelData\Data;

class ConfirmationDetailDTO extends Data
{
    public function __construct(
        public int $numConfirmations,
        public int $momentumHeight,
        public string $momentumHash,
        public int $momentumTimestamp,
    ) {}
}
