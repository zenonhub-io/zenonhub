<?php

declare(strict_types=1);

namespace App\Domains\Nom\DataTransferObjects;

use Spatie\LaravelData\Data;

class MomentumAcknowledgedData extends Data
{
    public function __construct(
        public string $hash,
        public int $height
    ) {
    }
}