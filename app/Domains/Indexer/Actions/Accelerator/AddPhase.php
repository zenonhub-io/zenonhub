<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Accelerator\PhaseCreated;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Services\CoinGecko;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AddPhase extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $project = AcceleratorProject::findBy('hash', $blockData['id']);

        if (! $project || ! $this->validateAction($accountBlock)) {
            Log::info('Contract Method Processor - Accelerator: AddPhase failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        $priceService = app(CoinGecko::class);
        $znnPrice = $priceService->historicPrice('zenon-2', 'usd', $accountBlock->created_at);
        $qsrPrice = $priceService->historicPrice('quasar', 'usd', $accountBlock->created_at);

        // Projects created before QSR price available
        if (is_null($qsrPrice) && $znnPrice) {
            $qsrPrice = $znnPrice / 10;
        }

        $phase = $project->phases()->create([
            'hash' => $accountBlock->hash,
            'name' => $blockData['name'],
            'slug' => Str::slug($blockData['name']),
            'url' => $blockData['url'],
            'description' => $blockData['description'],
            'znn_requested' => $blockData['znnFundsNeeded'],
            'qsr_requested' => $blockData['qsrFundsNeeded'],
            'znn_price' => $znnPrice ?: null,
            'qsr_price' => $qsrPrice ?: null,
            'created_at' => $accountBlock->created_at,
        ]);

        $project->modified_at = $accountBlock->created_at;
        $project->save();

        PhaseCreated::dispatch($accountBlock, $phase);

        Log::info('Contract Method Processor - Accelerator: AddPhase complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'phase' => $phase,
        ]);

        //(new UpdatePillarEngagementScores)->execute();

        $this->setBlockAsProcessed($accountBlock);
    }

    protected function validateAction(): bool
    {
        [$accountBlock] = func_get_args();

        return true;
    }

    private function notifyUsers(): void
    {
        $subscribedUsers = NotificationType::getSubscribedUsers('network-az');
        $networkBot = new \App\Bots\NetworkAlertBot;

        Notification::send(
            $subscribedUsers->prepend($networkBot),
            new \App\Notifications\Nom\Accelerator\PhaseAdded($this->phase)
        );
    }
}