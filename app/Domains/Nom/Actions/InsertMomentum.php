<?php

declare(strict_types=1);

namespace App\Domains\Nom\Actions;

use App\Domains\Nom\DataTransferObjects\MomentumData as MomentumDTO;
use App\Domains\Nom\Events\MomentumInserted;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Models\Pillar;
use App\Domains\Nom\Models\PillarHistory;
use Illuminate\Support\Facades\Log;

class InsertMomentum
{
    public function execute(MomentumDTO $momentumDTO): void
    {
        Log::debug('Insert Momentum', [
            'hash' => $momentumDTO->hash,
        ]);

        $chain = load_chain();
        $producer = load_account($momentumDTO->producer);
        $momentum = Momentum::where('hash', $momentumDTO->hash)->first();
        $pillar = Pillar::where('producer_id', $producer->id)->first();

        if (! $pillar) {
            $history = PillarHistory::where('producer_id', $producer->id)->first();
            if ($history) {
                $pillar = $history->pillar;
            }
        }

        if (! $momentum) {
            $momentum = Momentum::create([
                'chain_id' => $chain->id,
                'producer_account_id' => $producer->id,
                'producer_pillar_id' => $pillar?->id,
                'version' => $momentumDTO->version,
                'height' => $momentumDTO->height,
                'hash' => $momentumDTO->hash,
                'data' => $momentumDTO->data,
                'created_at' => $momentumDTO->timestamp,
            ]);
        }

        MomentumInserted::dispatch($momentum, $momentumDTO);
    }
}
