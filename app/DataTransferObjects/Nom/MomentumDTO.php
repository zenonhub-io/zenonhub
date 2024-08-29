<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Nom;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class MomentumDTO extends Data
{
    public function __construct(
        public int $version,
        public int $chainIdentifier,
        public string $hash,
        public string $previousHash,
        public int $height,
        public int $timestamp,
        public string $data,
        /** @var Collection<int, MomentumContentDTO> */
        public Collection $content,
        public string $changesHash,
        public ?string $publicKey,
        public ?string $signature,
        public string $producer,
    ) {
    }
}
