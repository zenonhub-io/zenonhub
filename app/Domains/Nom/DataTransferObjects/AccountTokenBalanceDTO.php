<?php

declare(strict_types=1);

namespace App\Domains\Nom\DataTransferObjects;

use Spatie\LaravelData\Data;

class AccountTokenBalanceDTO extends Data
{
    public function __construct(
        public TokenDTO $token,
        public string $balance,
    ) {
    }
}
