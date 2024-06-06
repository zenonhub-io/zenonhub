<?php

declare(strict_types=1);

namespace App\Domains\Nom\Actions;

use App\Domains\Nom\DataTransferObjects\PillarDTO;
use App\Domains\Nom\Exceptions\ZenonRpcException;
use App\Domains\Nom\Models\Pillar;
use App\Domains\Nom\Services\ZenonSdk;
use Lorisleiva\Actions\Concerns\AsAction;

class SyncPillarMetrics
{
    use AsAction;

    public function handle(Pillar $pillar): void
    {
        try {
            $pillarDTO = app(ZenonSdk::class)->getPillarByName($pillar->name);
        } catch (ZenonRpcException $e) {
            return;
        }

        $this->syncPillarMetrics($pillar, $pillarDTO);
    }

    private function syncPillarMetrics(Pillar $pillar, PillarDTO $pillarDTO): void
    {
        $missed = false;
        if (
            $pillar->produced_momentums === $pillarDTO->currentStats->producedMomentums &&
            $pillar->produced_momentums < $pillar->expected_momentums
        ) {
            $missed = true;
        }

        $pillar->rank = $pillarDTO->rank;
        $pillar->weight = $pillarDTO->weight;
        $pillar->produced_momentums = $pillarDTO->currentStats->producedMomentums;
        $pillar->expected_momentums = $pillarDTO->currentStats->expectedMomentums;

        if ($missed) {
            if ($pillar->missed_momentums < 999) {
                $pillar->missed_momentums++;
            }
        } elseif ($pillar->expected_momentums > 0) {
            $pillar->missed_momentums = 0;
        }

        $pillar->save();
    }
}
