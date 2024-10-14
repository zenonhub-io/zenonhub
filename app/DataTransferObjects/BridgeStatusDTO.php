<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Models\Nom\Account;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class BridgeStatusDTO extends Data
{
    public function __construct(
        public bool $bridgeOnline,
        public bool $orchestratorsOnline,
        public float $orchestratorsPercentage,
        public float $requiredOrchestratorsPercentage,
        public Account $bridgeAdmin,
        /** @var Collection<int, Account> */
        public ?Collection $bridgeGuardians,
    ) {}
}
