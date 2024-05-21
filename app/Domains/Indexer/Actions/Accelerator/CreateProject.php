<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Accelerator\ProjectCreated;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Services\CoinGecko;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class CreateProject extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('token');
        $blockData = $accountBlock->data->decoded;

        if (! $this->validateAction($accountBlock)) {
            Log::info('Contract Method Processor - Accelerator: CreateProject failed', [
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

        $project = AcceleratorProject::create([
            'chain_id' => $accountBlock->chain_id,
            'owner_id' => $accountBlock->account_id,
            'hash' => $accountBlock->hash,
            'name' => $blockData['name'],
            'slug' => Str::slug($blockData['name']),
            'url' => $blockData['url'],
            'description' => $blockData['description'],
            'znn_requested' => $blockData['znnFundsNeeded'],
            'qsr_requested' => $blockData['qsrFundsNeeded'],
            'znn_remaining' => $blockData['znnFundsNeeded'],
            'qsr_remaining' => $blockData['qsrFundsNeeded'],
            'znn_price' => $znnPrice ?: null,
            'qsr_price' => $qsrPrice ?: null,
            'created_at' => $accountBlock->created_at,
        ]);

        ProjectCreated::dispatch($accountBlock, $project);

        Log::info('Contract Method Processor - Accelerator: CreateProject complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'project' => $project,
        ]);

        //(new UpdatePillarEngagementScores)->execute();

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): bool
    {
        /**
         * @var AccountBlock $accountBlock
         */
        [$accountBlock] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if ($blockData['name'] === '' || strlen($blockData['name']) > config('nom.accelerator.projectNameLengthMax')) {
            return false;
        }

        if ($blockData['description'] === '' || strlen($blockData['description']) > config('nom.accelerator.projectDescriptionLengthMax')) {
            return false;
        }

        if ($blockData['znnFundsNeeded'] > config('nom.accelerator.projectZnnMaximumFunds')) {
            return false;
        }

        if ($blockData['qsrFundsNeeded'] > config('nom.accelerator.projectQsrMaximumFunds')) {
            return false;
        }

        if ($accountBlock->token->token_standard !== NetworkTokensEnum::ZNN->value) {
            return false;
        }

        if ($accountBlock->amount !== config('nom.accelerator.projectCreationAmount')) {
            return false;
        }

        return true;
    }

    private function notifyUsers(): void
    {
        $subscribedUsers = NotificationType::getSubscribedUsers('network-az');
        $networkBot = new \App\Bots\NetworkAlertBot;

        Notification::send(
            $subscribedUsers->prepend($networkBot),
            new \App\Notifications\Nom\Accelerator\ProjectCreated($this->project)
        );
    }
}
