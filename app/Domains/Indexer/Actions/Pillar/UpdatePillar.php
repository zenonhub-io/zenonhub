<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Pillar;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Pillar\PillarUpdated;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class UpdatePillar extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $pillar = Pillar::findBy('name', $blockData['name']);

        if (! $pillar || ! $this->validateAction($accountBlock, $pillar)) {
            Log::info('Contract Method Processor - Pillar: UpdatePillar failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

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

        $pillar->producer_account_id = $producerAddress->id;
        $pillar->withdraw_account_id = $rewardAddress->id;
        $pillar->momentum_rewards = $blockData['giveBlockRewardPercentage'];
        $pillar->delegate_rewards = $blockData['giveDelegateRewardPercentage'];
        $pillar->updated_at = $accountBlock->created_at;
        $pillar->save();

        $pillar->history()->create([
            'producer_account_id' => $producerAddress->id,
            'withdraw_account_id' => $rewardAddress->id,
            'momentum_rewards' => $blockData['giveBlockRewardPercentage'],
            'delegate_rewards' => $blockData['giveDelegateRewardPercentage'],
            'is_reward_change' => $rewardsChanged,
            'updated_at' => $accountBlock->created_at,
        ]);

        PillarUpdated::dispatch($accountBlock, $pillar);

        Log::info('Contract Method Processor - Pillar: UpdatePillar complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'pillar' => $pillar,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): bool
    {
        /**
         * @var AccountBlock $accountBlock
         * @var Pillar $pillar
         */
        [$accountBlock, $pillar] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if ($pillar->owner_id !== $accountBlock->account_id) {
            return false;
        }

        if ($pillar->revoked_at !== null) {
            return false;
        }

        if ($blockData['giveBlockRewardPercentage'] > 100 || $blockData['giveBlockRewardPercentage'] < 0) {
            return false;
        }

        if ($blockData['giveDelegateRewardPercentage'] > 100 || $blockData['giveDelegateRewardPercentage'] < 0) {
            return false;
        }

        return true;
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
