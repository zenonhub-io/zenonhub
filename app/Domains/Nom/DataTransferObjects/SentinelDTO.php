<?php

declare(strict_types=1);

namespace App\Domains\Nom\DataTransferObjects;

use Spatie\LaravelData\Data;

class SentinelDTO extends Data
{
    public function __construct(
        public string $owner,
        public int $registrationTimestamp,
        public bool $isRevocable,
        public int $revokeCooldown,
        public bool $active
    ) {
    }
}