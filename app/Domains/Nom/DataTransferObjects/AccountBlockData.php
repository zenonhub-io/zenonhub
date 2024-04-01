<?php

declare(strict_types=1);

namespace App\Domains\Nom\DataTransferObjects;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class AccountBlockData extends Data
{
    public function __construct(
        public int $version,
        public int $chainIdentifier,
        public int $blockType,
        public string $hash,
        public string $previousHash,
        public int $height,
        public ?MomentumAcknowledgedData $momentumAcknowledged,
        public string $address,
        public string $toAddress,
        public string $amount,
        public string $tokenStandard,
        public string $fromBlockHash,
        /** @var Collection<int, AccountBlockData> */
        public ?Collection $descendantBlocks,
        public ?string $data,
        public int $fusedPlasma,
        public int $difficulty,
        public string $nonce,
        public int $basePlasma,
        public int $usedPlasma,
        public string $changesHash,
        public ?string $publicKey,
        public ?string $signature,
        public ?TokenData $token,
        public ?ConfirmationDetailData $confirmationDetail,
        public ?AccountBlockData $pairedAccountBlock,
    ) {
    }
}
