<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Nom;

use Spatie\LaravelData\Data;

class BridgeInfoDTO extends Data
{
    public function __construct(
        public string $administrator,
        public string $compressedTssECDSAPubKey,
        public string $decompressedTssECDSAPubKey,
        public bool $allowKeyGen,
        public bool $halted,
        public int $unhaltedAt,
        public int $unhaltDurationInMomentums,
        public int $tssNonce,
        public string $metadata,
    ) {}
}
