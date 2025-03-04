<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Models\Nom\BridgeAdmin;
use App\Models\Nom\BridgeGuardian;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class BridgeStatusDTO extends Data
{
    public function __construct(
        public bool $bridgeOnline,
        public bool $orchestratorsOnline,
        public ?float $orchestratorsOnlinePercentage,
        public ?float $orchestratorsRequiredOnlinePercentage,
        public int $totalOrchestratorsCount,
        public int $totalOrchestratorsOnlineCount,
        public int $totalOrchestratorsOfflineCount,
        public bool $isHalted,
        public int $unhaltedAt,
        public int $unhaltDurationInMomentums,
        public ?int $estimatedUnhaltHeight,
        public ?int $estimatedMomentumsUntilUnhalt,
        // public ?int $pendingIncomingTx,
        // public ?int $pendingOutgoingTx,
        public bool $allowKeyGen,
        public string $compressedTssECDSAPubKey,
        public string $decompressedTssECDSAPubKey,
        public BridgeAdmin $bridgeAdmin,
        /** @var Collection<int, BridgeGuardian> */
        public ?Collection $bridgeGuardians,
        public Carbon $updatedAt,
    ) {}
}
