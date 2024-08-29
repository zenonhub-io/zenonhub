<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Nom;

use Spatie\LaravelData\Data;

class PillarDTO extends Data
{
    public function __construct(
        public string $name,
        public int $rank,
        public int $type,
        public string $ownerAddress,
        public string $producerAddress,
        public string $withdrawAddress,
        public bool $isRevocable,
        public int $revokeCooldown,
        public int $revokeTimestamp,
        public int $giveMomentumRewardPercentage,
        public int $giveDelegateRewardPercentage,
        public PillarCurrentStatsDTO $currentStats,
        public string $weight
    ) {
    }
}
