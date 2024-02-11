<?php

namespace App\Jobs\Nom\Pillar;

use App\Actions\SetBlockAsProcessed;
use App\Classes\Utilities;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Pillar;
use App\Models\Nom\PillarHistory;
use App\Models\NotificationType;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class UpdatePillar implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;

    public int $backoff = 10;

    public AccountBlock $block;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        $blockData = $this->block->data->decoded;
        $pillar = Pillar::where('owner_id', $this->block->account->id)->first();

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

        $producerAddress = Utilities::loadAccount($blockData['producerAddress']);
        $rewardAddress = Utilities::loadAccount($blockData['rewardAddress']);

        PillarHistory::create([
            'pillar_id' => $pillar->id,
            'producer_id' => $producerAddress?->id,
            'withdraw_id' => $rewardAddress?->id,
            'momentum_rewards' => $blockData['giveBlockRewardPercentage'],
            'delegate_rewards' => $blockData['giveDelegateRewardPercentage'],
            'is_reward_change' => $rewardsChanged,
            'updated_at' => $this->block->momentum->created_at,
        ]);

        $pillar->momentum_rewards = $blockData['giveBlockRewardPercentage'];
        $pillar->delegate_rewards = $blockData['giveDelegateRewardPercentage'];
        $pillar->producer_id = $producerAddress?->id;
        $pillar->withdraw_id = $rewardAddress?->id;
        $pillar->save();
        $pillar->refresh();

        if ($rewardsChanged) {
            $this->notifyUsers($pillar);
        }

        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function notifyUsers($pillar): void
    {
        // any pillar updated
        $subscribedUsers = NotificationType::getSubscribedUsers('network-pillar');
        $networkBot = new \App\Bots\NetworkAlertBot();

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
