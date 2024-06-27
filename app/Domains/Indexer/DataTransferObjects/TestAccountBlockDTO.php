<?php

declare(strict_types=1);

namespace App\Domains\Indexer\DataTransferObjects;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Models\Token;
use Spatie\LaravelData\Data;

class TestAccountBlockDTO extends Data
{
    public function __construct(
        public Account $account,
        public Account $toAccount,
        public ?Momentum $momentum,
        public ?Momentum $momentumAcknowledged,
        public ?Token $token = null,
        public ?int $height = 1,
        public ?string $amount = '0',
        public ?ContractMethod $contractMethod = null,
        public ?array $data = [],
    ) {
    }
}
