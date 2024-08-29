<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Nom;

use Spatie\LaravelData\Data;

class AccountTokenBalanceDTO extends Data
{
    public function __construct(
        public TokenDTO $token,
        public string $balance,
    ) {
    }
}
