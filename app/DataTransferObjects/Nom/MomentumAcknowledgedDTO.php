<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Nom;

use Spatie\LaravelData\Data;

class MomentumAcknowledgedDTO extends Data
{
    public function __construct(
        public string $hash,
        public int $height
    ) {}
}
