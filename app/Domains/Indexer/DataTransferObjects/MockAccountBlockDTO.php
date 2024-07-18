<?php

declare(strict_types=1);

namespace App\Domains\Indexer\DataTransferObjects;

use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Models\Token;
use Spatie\LaravelData\Data;

class MockAccountBlockDTO extends Data
{
    public function __construct(
        public Account $account,
        public Account $toAccount,
        public ?AccountBlockTypesEnum $blockType,
        public ?Token $token,
        public ?string $amount,
        public ?ContractMethod $contractMethod,
        public array|string|null $data,
        public ?Momentum $momentum,
        public ?Momentum $momentumAcknowledged,
        public ?int $height,
    ) {

        if (! $this->momentum) {
            $this->momentum = Momentum::getFrontier();
        }

        if (! $this->momentumAcknowledged) {
            $this->momentumAcknowledged = Momentum::getFrontier();
        }

        if (! $this->height) {
            $this->height = AccountBlock::max('height') + 1;
        }

        if (! $this->blockType) {
            $this->blockType = AccountBlockTypesEnum::SEND;
        }

        if (is_string($this->data)) {
            $this->data = json_decode($this->data, true);
        }
    }
}
