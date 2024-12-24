<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use App\Models\Nom\Momentum;
use App\Models\Nom\Pillar;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class SetMissingMomentumProducers
{
    use AsAction;

    public string $commandSignature = 'nom:set-missing-momentum-producers';

    public function handle(Momentum $momentum): void
    {
        Log::debug('Set missing momentum producer - Start');

        if ($momentum->producer_pillar_id) {
            return;
        }

        $pillar = Pillar::where('producer_account_id', $momentum->producer_account_id)
            ->orWhereHas('updateHistory', function ($query) use ($momentum) {
                $query->where('producer_account_id', $momentum->producer_account_id)
                    ->whereDate('updated_at', '<=', $momentum->created_at->format('Y-m-d'));
            })
            ->first();

        if (! $pillar) {
            $momentum->loadMissing(['producerAccount']);

            Log::debug('Set missing momentum producer - Unable to find pillar for momentum', [
                'hash' => $momentum->hash,
                'producer' => $momentum->producerAccount->address,
            ]);

            return;
        }

        $momentum->producer_pillar_id = $pillar->id;
        $momentum->save();
    }

    public function asCommand(Command $command): void
    {
        $totalUnwraps = Momentum::whereNull('producer_pillar_id')->count();
        $progressBar = new ProgressBar(new ConsoleOutput, $totalUnwraps);
        $progressBar->start();

        Momentum::whereNull('producer_pillar_id')
            ->chunk(1000, function (Collection $momentums) use ($progressBar) {
                $momentums->each(function ($momentum) use ($progressBar) {
                    $this->handle($momentum);
                    $progressBar->advance();
                });
            });

        $progressBar->finish();
    }
}
