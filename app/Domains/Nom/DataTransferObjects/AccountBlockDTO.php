<?php

declare(strict_types=1);

namespace App\Domains\Nom\DataTransferObjects;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class AccountBlockDTO extends Data
{
    public function __construct(
        public int $version,
        public int $chainIdentifier,
        public int $blockType,
        public string $hash,
        public string $previousHash,
        public int $height,
        public MomentumAcknowledgedDTO $momentumAcknowledged,
        public string $address,
        public string $toAddress,
        public string $amount,
        public string $tokenStandard,
        public string $fromBlockHash,
        /** @var Collection<int, AccountBlockDTO> */
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
        public ?TokenDTO $token,
        public ?ConfirmationDetailDTO $confirmationDetail,
        public ?AccountBlockDTO $pairedAccountBlock,
    ) {
    }
}
