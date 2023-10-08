<?php

namespace App\Jobs\Nom\Pillar;

use App\Actions\SetBlockAsProcessed;
use App\Classes\Utilities;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Pillar;
use App\Models\NotificationType;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class Register implements ShouldQueue
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

        $pillar = Pillar::where('name', $blockData['name'])->first();

        if (! $pillar) {
            $producerAddress = Utilities::loadAccount($blockData['producerAddress']);
            $withdrawAddress = Utilities::loadAccount(($blockData['withdrawAddress'] ?? $blockData['rewardAddress']));

            $pillar = Pillar::create([
                'chain_id' => $this->block->chain->id,
                'owner_id' => $this->block->account->id,
                'producer_id' => $producerAddress?->id,
                'withdraw_id' => $withdrawAddress?->id,
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

        $qsrBurnData = $this->block->paired_account_block?->descendants()
            ->whereHas('contract_method', function ($q) {
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
        $pillar->created_at = $this->block->momentum->created_at;
        $pillar->is_legacy = false;
        $pillar->save();

        $this->notifyUsers($pillar);
        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function notifyUsers($pillar): void
    {
        $subscribedUsers = NotificationType::getSubscribedUsers('network-pillar');

        Notification::send(
            $subscribedUsers,
            new \App\Notifications\Nom\Pillar\Registered($pillar)
        );
    }
}
