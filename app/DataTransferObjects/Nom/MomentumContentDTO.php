<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Nom;

use Spatie\LaravelData\Data;

class MomentumContentDTO extends Data
{
    public function __construct(
        public string $address,
        public string $hash,
        public int $height,
    ) {}
}
