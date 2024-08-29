<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Enums\Nom\AccountBlockTypesEnum;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Momentum;
use App\Models\Nom\Token;
use Carbon\Carbon;
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
        public ?Carbon $createdAt,
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

        if (! $this->amount) {
            $this->amount = '0';
        }

        if (! $this->blockType) {
            $this->blockType = AccountBlockTypesEnum::SEND;
        }

        if (is_string($this->data)) {
            $this->data = json_decode($this->data, true);
        }
    }
}
