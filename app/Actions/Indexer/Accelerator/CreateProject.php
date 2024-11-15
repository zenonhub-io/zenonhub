<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Accelerator;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Accelerator\ProjectCreated;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\AccountBlock;
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

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Accelerator: CreateProject failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $znnPrice = app('znnToken')->price;
        $qsrPrice = app('qsrToken')->price;

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

    /**
     * @throws IndexerActionValidationException
     */
    public function validateAction(): void
    {
        /**
         * @var AccountBlock $accountBlock
         */
        [$accountBlock] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if ($blockData['name'] === '' || strlen($blockData['name']) > config('nom.accelerator.projectNameLengthMax')) {
            throw new IndexerActionValidationException('Invalid name');
        }

        if ($blockData['description'] === '' || strlen($blockData['description']) > config('nom.accelerator.projectDescriptionLengthMax')) {
            throw new IndexerActionValidationException('Invalid description');
        }

        if ($blockData['znnFundsNeeded'] > config('nom.accelerator.projectZnnMaximumFunds')) {
            throw new IndexerActionValidationException('Max ZNN funds exceeded');
        }

        if ($blockData['qsrFundsNeeded'] > config('nom.accelerator.projectQsrMaximumFunds')) {
            throw new IndexerActionValidationException('Max QSR funds exceeded');
        }

        if ($accountBlock->token->token_standard !== NetworkTokensEnum::ZNN->value) {
            throw new IndexerActionValidationException('Token fee must be ZNN');
        }

        if ($accountBlock->amount !== config('nom.accelerator.projectCreationAmount')) {
            throw new IndexerActionValidationException('Creation fee amount is invalid');
        }
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
