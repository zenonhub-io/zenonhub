<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\BridgeStatusDTO;
use App\Models\Nom\BridgeAdmin;
use App\Models\Nom\TimeChallenge;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class BridgeStatus
{
    private BridgeStatusDTO $bridgeStatusDTO;

    public function __construct()
    {
        $this->bridgeStatusDTO = Cache::get('nom.bridge-status');
    }

    public function isBridgeOnline(): bool
    {
        return $this->bridgeStatusDTO->bridgeOnline;
    }

    public function isOrchestratorsOnline(): bool
    {
        return $this->bridgeStatusDTO->orchestratorsOnline;
    }

    public function isKeyGenAllowed(): bool
    {
        return $this->bridgeStatusDTO->allowKeyGen;
    }

    public function getAdminDelay(): int
    {
        return config('nom.bridge.minAdministratorDelay');
    }

    public function getSoftDelay(): int
    {
        return config('nom.bridge.minSoftDelay');
    }

    public function getUnhaltHeight(): ?int
    {
        return $this->bridgeStatusDTO->estimatedUnhaltHeight;
    }

    public function getMomentumsToUnhalt(): ?int
    {
        return $this->bridgeStatusDTO->estimatedMomentumsUntilUnhalt;
    }

    public function getBridgeAdmin(): BridgeAdmin
    {
        return $this->bridgeStatusDTO->bridgeAdmin;
    }

    public function getBridgeGuardians(): Collection
    {
        return $this->bridgeStatusDTO->bridgeGuardians;
    }

    public function getTimeChallenges(): array
    {
        return TimeChallenge::whereActive()
            ->whereHas('contractMethod', function ($query) {
                $query->whereRelation('contract', 'name', 'Bridge');
            })
            ->get();
    }
}
