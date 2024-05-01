<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Pillar;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use App\Domains\Nom\Models\PillarHistory;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Notification;

class UpdatePillar extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $this->accountBlock->data->decoded;
        $pillar = Pillar::where('owner_id', $this->accountBlock->account->id)->first();

        if (! $pillar) {
            return;
        }

        $rewardsChanged = false;
        if (
            $pillar->momentum_rewards !== (int) $blockData['giveBlockRewardPercentage'] ||
            $pillar->delegate_rewards !== (int) $blockData['giveDelegateRewardPercentage']
        ) {
            $rewardsChanged = true;
        }

        $producerAddress = load_account($blockData['producerAddress']);
        $rewardAddress = load_account($blockData['rewardAddress']);

        PillarHistory::create([
            'pillar_id' => $pillar->id,
            'producer_account_id' => $producerAddress?->id,
            'withdraw_account_id' => $rewardAddress?->id,
            'momentum_rewards' => $blockData['giveBlockRewardPercentage'],
            'delegate_rewards' => $blockData['giveDelegateRewardPercentage'],
            'is_reward_change' => $rewardsChanged,
            'updated_at' => $this->accountBlock->momentum->created_at,
        ]);

        $pillar->momentum_rewards = $blockData['giveBlockRewardPercentage'];
        $pillar->delegate_rewards = $blockData['giveDelegateRewardPercentage'];
        $pillar->producer_account_id = $producerAddress?->id;
        $pillar->withdraw_account_id = $rewardAddress?->id;
        $pillar->save();
        $pillar->refresh();

        if ($rewardsChanged) {
            $this->notifyUsers($pillar);
        }

    }

    private function notifyUsers($pillar): void
    {
        // any pillar updated
        $subscribedUsers = NotificationType::getSubscribedUsers('network-pillar');
        $networkBot = new \App\Bots\NetworkAlertBot;

        Notification::send(
            $subscribedUsers->prepend($networkBot),
            new \App\Notifications\Nom\Pillar\Updated($pillar)
        );

        // delegating pillar updated
        //        $notificationType = NotificationType::findByCode('delegating-pillar-updated');
        //        $subscribedUsers = User::whereHas('notification_types', fn ($query) => $query->where('code', $notificationType->code))
        //            ->whereHas('nom_accounts', function ($query) use ($pillar) {
        //                $query->whereHas('delegations', function ($query2) use ($pillar) {
        //                    $query2->where('pillar_id', $pillar->id)
        //                        ->whereNull('ended_at');
        //                });
        //            })
        //            ->get();
        //
        //        Notification::send(
        //            $subscribedUsers,
        //            new \App\Notifications\Pillar\DelegatingUpdated($pillar)
        //        );
    }
}
