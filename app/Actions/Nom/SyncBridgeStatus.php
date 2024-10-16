<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use App\DataTransferObjects\BridgeStatusDTO;
use App\DataTransferObjects\Nom\BridgeInfoDTO;
use App\Models\Nom\BridgeAdmin;
use App\Models\Nom\BridgeGuardian;
use App\Models\Nom\Momentum;
use App\Models\Nom\Orchestrator;
use App\Services\ZenonSdk\ZenonSdk;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class SyncBridgeStatus
{
    use AsAction;

    public function handle(): void
    {
        Log::debug('Sync Bridge Status - Start');

        $bridgeInfo = $this->getBridgeInfo();

        if (! $bridgeInfo) {
            return;
        }

        $bridgeStatus = $this->buildBridgeStatusDTO($bridgeInfo);

        Log::debug('Sync Bridge Status - Update cache');
        Cache::forever('nom.bridge-status', $bridgeStatus);
    }

    private function getBridgeInfo(): ?BridgeInfoDTO
    {
        $sdk = app(ZenonSdk::class);

        try {
            Log::debug('Sync Bridge Status - Get bridge info');

            return $sdk->getBridgeInfo();
        } catch (Throwable $exception) {
            Log::warning('Sync Bridge Status - Unable to call getBridgeInfo');

            return null;
        }
    }

    private function buildBridgeStatusDTO(BridgeInfoDTO $bridgeInfo): BridgeStatusDTO
    {
        $orchestratorsOnlinePercentage = $this->getOrchestratorOnlinePercent();
        $orchestratorsRequiredOnlinePercentage = $this->getOrchestratorRequiredOnlinePercent();
        $estimatedUnhaltHeight = $this->getEstimatedUnhaltHeight($bridgeInfo->unhaltedAt, $bridgeInfo->unhaltDurationInMomentums);
        $estimatedMomentumsUntilUnhalt = $this->getEstimatedMomentumsUntilUnhalt($estimatedUnhaltHeight);
        $bridgeOnline = $this->getBridgeOnline($bridgeInfo->halted, $estimatedUnhaltHeight);

        return BridgeStatusDTO::from([
            'bridgeOnline' => $bridgeOnline,
            'orchestratorsOnline' => $orchestratorsOnlinePercentage >= $orchestratorsRequiredOnlinePercentage,
            'orchestratorsOnlinePercentage' => $orchestratorsOnlinePercentage,
            'orchestratorsRequiredOnlinePercentage' => $orchestratorsRequiredOnlinePercentage,
            'isHalted' => $bridgeInfo->halted,
            'unhaltedAt' => $bridgeInfo->unhaltedAt,
            'unhaltDurationInMomentums' => $bridgeInfo->unhaltDurationInMomentums,
            'estimatedUnhaltHeight' => $estimatedUnhaltHeight,
            'estimatedMomentumsUntilUnhalt' => $estimatedMomentumsUntilUnhalt,
            'allowKeyGen' => $bridgeInfo->allowKeyGen,
            'compressedTssECDSAPubKey' => $bridgeInfo->compressedTssECDSAPubKey,
            'decompressedTssECDSAPubKey' => $bridgeInfo->decompressedTssECDSAPubKey,
            'bridgeAdmin' => BridgeAdmin::getActiveAdmin(),
            'bridgeGuardians' => BridgeGuardian::getActiveGuardians(),
            'updatedAt' => now(),
        ]);
    }

    private function getOrchestratorOnlinePercent(): float
    {
        $total = Orchestrator::count();
        $online = Orchestrator::whereActive()->count();
        $percent = ($online / $total) * 100;

        return round($percent, 1);
    }

    private function getOrchestratorRequiredOnlinePercent(): float
    {
        $total = Orchestrator::count();
        $required = ceil($total * 0.66) + 1;
        $percent = ($required / $total) * 100;

        return round($percent, 1);
    }

    private function getEstimatedUnhaltHeight(int $unhaltedAt, int $unhaltDurationInMomentums): ?int
    {
        $currentMomentum = Momentum::getFrontier();
        $unhaltHeight = ($unhaltedAt + $unhaltDurationInMomentums);

        if ($unhaltHeight > $currentMomentum->height) {
            return $unhaltHeight;
        }

        return null;
    }

    private function getEstimatedMomentumsUntilUnhalt(?int $unhaltHeight): ?int
    {
        if (! $unhaltHeight) {
            return null;
        }

        $currentMomentum = Momentum::getFrontier();
        $unhaltIn = ($unhaltHeight - $currentMomentum->height);

        if ($unhaltIn > 0) {
            return $unhaltIn;
        }

        return null;
    }

    private function getBridgeOnline(bool $halted, ?int $estimatedUnhaltHeight): bool
    {
        return ! ($estimatedUnhaltHeight || $halted);
    }
}
