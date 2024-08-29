<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ApplicationException;
use App\Models\Nom\Momentum;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class BridgeStatus
{
    protected $sdk;

    protected ?object $bridgeInfoJson;

    protected ?array $timeChallengesJson;

    protected ?object $bridgeSecurityJson;

    public function __construct()
    {
        $this->sdk = App::make(ZenonSdk::class);
        $this->loadCaches();
    }

    //
    // Public

    public function clearCache(): void
    {
        $bridgeInfoJson = $this->bridgeInfoJson;
        $timeChallengesJson = $this->timeChallengesJson;
        $bridgeSecurityJson = $this->bridgeSecurityJson;

        try {
            Cache::forget('service.bridgeStatus.bridgeInfoJson');
            Cache::forget('service.bridgeStatus.timeChallengesJson');
            Cache::forget('service.bridgeStatus.bridgeSecurityJson');
            $this->loadCaches();
        } catch (Throwable $exception) {
            Log::warning($exception->getMessage());
            Cache::forever('service.bridgeStatus.bridgeInfoJson', $bridgeInfoJson);
            Cache::forever('service.bridgeStatus.timeChallengesJson', $timeChallengesJson);
            Cache::forever('service.bridgeStatus.bridgeSecurityJson', $bridgeSecurityJson);
        }
    }

    public function getAdministrator(): string
    {
        return $this->bridgeInfoJson->administrator;
    }

    public function getGuardians(): array
    {
        return $this->bridgeSecurityJson->guardians;
    }

    public function getIsHalted(): bool
    {
        if ($this->getEstimatedUnhaltMonemtum()) {
            return true;
        }

        return $this->bridgeInfoJson->halted;
    }

    public function getEstimatedUnhaltMonemtum(): ?int
    {
        $adminUnhalted = $this->bridgeInfoJson->unhaltedAt;
        $unhaltDelay = $this->bridgeInfoJson->unhaltDurationInMomentums;
        $currentHeight = Momentum::max('height');
        $unhaltAt = ($adminUnhalted + $unhaltDelay);
        $unhaltIn = ($unhaltAt - $currentHeight);

        if ($unhaltAt > $currentHeight && $unhaltIn > 0) {
            return $unhaltIn;
        }

        return null;
    }

    public function getAllowKeygen(): bool
    {
        return $this->bridgeInfoJson->allowKeyGen;
    }

    public function getAdminDelay(): int
    {
        return $this->bridgeSecurityJson->administratorDelay;
    }

    public function getSoftDelay(): int
    {
        return $this->bridgeSecurityJson->softDelay;
    }

    public function getTimeChallenges(): array
    {
        $activeChallenges = [];
        $networkHeight = Momentum::max('height');

        foreach ($this->timeChallengesJson as $challenge) {
            $startHeight = $challenge->ChallengeStartHeight;
            $endHeight = $startHeight + $this->getSoftDelay();

            $activeChallenges[] = [
                'name' => $challenge->MethodName,
                'isActive' => ($networkHeight < $endHeight),
                'startHeight' => $startHeight,
                'endHeight' => $endHeight,
                'endsIn' => $endHeight - $networkHeight,
            ];
        }

        return $activeChallenges;
    }

    protected function getBridgeInfo()
    {
        try {
            return $this->sdk->bridge->getBridgeInfo()['data'];
        } catch (Throwable $exception) {
            throw new ApplicationException('Unable to call getBridgeInfo');
        }
    }

    protected function getBridgeTimeChallenges()
    {
        try {
            return $this->sdk->bridge->getTimeChallengesInfo()['data']->list;
        } catch (Throwable $exception) {
            throw new ApplicationException('Unable to call getTimeChallengesInfo');
        }
    }

    protected function getSecurityInfo()
    {
        try {
            return $this->sdk->bridge->getSecurityInfo()['data'];
        } catch (Throwable $exception) {
            throw new ApplicationException('Unable to call getSecurityInfo');
        }
    }

    //
    // Private

    private function loadCaches(): void
    {
        $this->bridgeInfoJson = Cache::rememberForever('service.bridgeStatus.bridgeInfoJson', fn () => $this->getBridgeInfo());

        $this->timeChallengesJson = Cache::rememberForever('service.bridgeStatus.timeChallengesJson', fn () => $this->getBridgeTimeChallenges());

        $this->bridgeSecurityJson = Cache::rememberForever('service.bridgeStatus.bridgeSecurityJson', fn () => $this->getSecurityInfo());
    }
}
