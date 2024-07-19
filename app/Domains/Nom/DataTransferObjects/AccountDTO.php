<?php

declare(strict_types=1);

namespace App\Domains\Nom\DataTransferObjects;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class AccountDTO extends Data
{
    public function __construct(
        public string $address,
        public int $accountHeight,
        /** @var Collection<int, AccountTokenBalanceDTO> */
        public ?Collection $balanceInfoMap
    ) {
    }
}
