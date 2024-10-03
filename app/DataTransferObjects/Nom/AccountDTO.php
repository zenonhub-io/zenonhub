<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Nom;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class AccountDTO extends Data
{
    public function __construct(
        public string $address,
        public int $accountHeight,
        /** @var Collection<int, AccountTokenBalanceDTO> */
        public ?Collection $balanceInfoMap
    ) {}
}
