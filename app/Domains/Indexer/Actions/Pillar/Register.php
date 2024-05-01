<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Pillar;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class Register extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $this->accountBlock->data->decoded;

        $pillar = Pillar::where('name', $blockData['name'])->first();

        if (! $pillar) {
            $producerAddress = load_account($blockData['producerAddress']);
            $withdrawAddress = load_account(($blockData['withdrawAddress'] ?? $blockData['rewardAddress']));

            $pillar = Pillar::create([
                'chain_id' => $this->accountBlock->chain->id,
                'owner_id' => $this->accountBlock->account->id,
                'producer_account_id' => $producerAddress?->id,
                'withdraw_account_id' => $withdrawAddress?->id,
                'name' => $blockData['name'],
                'slug' => Str::slug($blockData['name']),
                'weight' => 0,
                'produced_momentums' => 0,
                'expected_momentums' => 0,
                'momentum_rewards' => $blockData['giveBlockRewardPercentage'],
                'delegate_rewards' => $blockData['giveDelegateRewardPercentage'],
                'is_legacy' => false,
            ]);
        }

        $qsrBurnData = $this->accountBlock->pairedAccountBlock?->descendants()
            ->whereHas('contractMethod', function ($q) {
                $q->whereHas('contract', fn ($q) => $q->where('name', 'Token'))
                    ->where('name', ' Burn');
            })
            ->first();

        if ($qsrBurnData) {
            $qsrBurn = $qsrBurnData->amount;
        } else {
            $qsrBurn = Pillar::max('qsr_burn') + config('zenon.pillar_qsr_burn_increment');
        }

        $pillar->qsr_burn = $qsrBurn;
        $pillar->created_at = $this->accountBlock->momentum->created_at;
        $pillar->is_legacy = false;
        $pillar->save();

        $this->notifyUsers($pillar);

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
