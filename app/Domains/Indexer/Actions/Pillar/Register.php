<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Pillar;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Pillar\PillarRegistered;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class Register extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $this->accountBlock = $accountBlock->load('pairedAccountBlock');
        $blockData = $accountBlock->data->decoded;

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

        $this->setBlockAsProcessed();
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
