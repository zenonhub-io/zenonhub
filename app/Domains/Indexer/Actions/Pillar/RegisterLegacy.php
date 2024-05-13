<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Pillar;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class RegisterLegacy extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        $producerAddress = load_account($blockData['producerAddress']);
        $withdrawAddress = load_account(($blockData['withdrawAddress'] ?? $blockData['rewardAddress']));

        Pillar::create([
            'chain_id' => $accountBlock->chain->id,
            'owner_id' => $accountBlock->account->id,
            'producer_account_id' => $producerAddress->id,
            'withdraw_account_id' => $withdrawAddress->id,
            'name' => $blockData['name'],
            'slug' => Str::slug($blockData['name']),
            'qsr_burn' => 15000000000000,
            'momentum_rewards' => $blockData['giveBlockRewardPercentage'],
            'delegate_rewards' => $blockData['giveDelegateRewardPercentage'],
            'is_legacy' => true,
            'created_at' => $accountBlock->created_at,
        ]);
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
