<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use App\Models\Nom\Pillar;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class SetMissingMomentumProducers
{
    use AsAction;

    public string $commandSignature = 'nom:set-missing-momentum-producers';

    public function handle(int $producerAccountId): void
    {
        Log::debug('Set missing momentum producer - Start');

        $pillar = Pillar::where('producer_account_id', $producerAccountId)
            ->orWhereRelation('updateHistory', 'producer_account_id', $producerAccountId)
            ->first();

        if (! $pillar) {

            Log::debug('Set missing momentum producer - Unable to find pillar', [
                'producerId' => $producerAccountId,
            ]);

            return;
        }

        DB::table('nom_momentums')
            ->where('producer_account_id', $producerAccountId)
            ->update([
                'producer_pillar_id' => $pillar->id,
            ]);
    }

    public function asCommand(Command $command): void
    {
        $momentums = DB::table('nom_momentums')
            ->whereNull('producer_pillar_id')
            ->groupBy('producer_account_id')
            ->count();
        $progressBar = new ProgressBar(new ConsoleOutput, $momentums);
        $progressBar->start();

        DB::table('nom_momentums')
            ->select('id', 'producer_account_id')
            ->whereNull('producer_pillar_id')
            ->groupBy('producer_account_id')
            ->chunkById(1000, function (Collection $momentums) use ($progressBar) {
                $momentums->each(function ($momentum) use ($progressBar) {
                    $this->handle($momentum->producer_account_id);
                    $progressBar->advance();
                });
            });

        $progressBar->finish();
    }
}
