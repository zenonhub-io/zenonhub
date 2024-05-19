<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Pillar;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Pillar\PillarRegistered;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class Register extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('pairedAccountBlock');
        $blockData = $accountBlock->data->decoded;

        if (! $this->validateAction($accountBlock)) {
            Log::info('Contract Method Processor - Pillar: Register failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        $qsrBurnData = $accountBlock->pairedAccountBlock?->descendants()
            ->whereHas('contractMethod', function ($q) {
                $q->whereHas('contract', fn ($q) => $q->where('name', 'Token'))
                    ->where('name', 'Burn');
            })
            ->first();

        $pillar = Pillar::updateOrCreate([
            'name' => $blockData['name'],
            'slug' => Str::slug($blockData['name']),
            'chain_id' => $accountBlock->chain_id,
            'owner_id' => $accountBlock->account_id,
        ], [
            'producer_account_id' => load_account($blockData['producerAddress'])->id,
            'withdraw_account_id' => load_account(($blockData['withdrawAddress'] ?? $blockData['rewardAddress']))->id,
            'qsr_burn' => $qsrBurnData?->amount ?? (Pillar::max('qsr_burn') + 1000000000000),
            'momentum_rewards' => $blockData['giveBlockRewardPercentage'],
            'delegate_rewards' => $blockData['giveDelegateRewardPercentage'],
            'is_legacy' => false,
            'created_at' => $accountBlock->created_at,
        ]);

        PillarRegistered::dispatch($accountBlock, $pillar);

        Log::info('Contract Method Processor - Pillar: Register complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'pillar' => $pillar,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    protected function validateAction(): bool
    {
        /**
         * @var AccountBlock $accountBlock
         */
        [$accountBlock] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if ($blockData['name'] === '' || strlen($blockData['name']) > config('nom.pillar.nameLengthMax')) {
            return false;
        }

        if (! preg_match('/^([a-zA-Z0-9]+[-._]?)*[a-zA-Z0-9]$/', $blockData['name'])) {
            return false;
        }

        if ($blockData['giveBlockRewardPercentage'] > 100 || $blockData['giveBlockRewardPercentage'] < 0) {
            return false;
        }

        if ($blockData['giveDelegateRewardPercentage'] > 100 || $blockData['giveDelegateRewardPercentage'] < 0) {
            return false;
        }

        if ($accountBlock->token->token_standard !== NetworkTokensEnum::ZNN->value) {
            return false;
        }

        if ($accountBlock->amount !== config('nom.pillar.znnStakeAmount')) {
            return false;
        }

        return true;
    }

    private function notifyUsers($pillar): void
    {
        $subscribedUsers = NotificationType::getSubscribedUsers('network-pillar');
        $networkBot = new \App\Bots\NetworkAlertBot;

        Notification::send(
            $subscribedUsers->prepend($networkBot),
            new \App\Notifications\Nom\Pillar\Registered($pillar)
        );
    }
}
