<?php

declare(strict_types=1);

namespace App\Domains\Nom\DataTransferObjects;

use Spatie\LaravelData\Data;

class TokenData extends Data
{
    public function __construct(
        public string $name,
        public string $symbol,
        public string $domain,
        public string $totalSupply,
        public int $decimals,
        public string $owner,
        public string $tokenStandard,
        public string $maxSupply,
        public bool $isBurnable,
        public bool $isMintable,
        public bool $isUtility,
    ) {
    }
}