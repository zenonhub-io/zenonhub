<?php

namespace App\Jobs\Token;

use App\Classes\Utilities;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\AccountReward;
use App\Models\Nom\TokenMint;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Mint implements ShouldQueue
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
        $this->processMint();
        $this->processRewards();
    }

    private function processMint()
    {
        $data = $this->block->data->decoded;
        $account = Utilities::loadAccount($data['receiveAddress']);

        TokenMint::create([
            'token_id' => $this->block->token?->id,
            'issuer_id' => $this->block->account->id,
            'receiver_id' => $account->id,
            'account_block_id' => $this->block->id,
            'amount' => $data['amount'],
            'created_at' => $this->block->created_at,
        ]);
    }

    private function processRewards()
    {
        if (! $this->block->parent) {
            return;
        }

        // Reward to address
        $rewardToAccount = $this->block->parent->paired_account_block->account;

        // Reward type block
        $fromAccount = $this->block->parent->account;
        $fromAddress = $fromAccount->address;

        // Amount block
        if (! $this->block->paired_account_block || $this->block->paired_account_block->account->address !== Account::ADDRESS_TOKEN) {
            return;
        }

        $amountBlock = $this->block->paired_account_block->descendants->first();

        // Type
        $rewardType = null;
        if($fromAddress === Account::ADDRESS_SENTINEL) {
            $rewardType = AccountReward::TYPE_SENTINEL;
        } elseif($fromAddress === Account::ADDRESS_STAKE) {
            $rewardType = AccountReward::TYPE_STAKE;
        } elseif($fromAddress === Account::ADDRESS_LIQUIDITY) {
            $rewardType = AccountReward::TYPE_LIQUIDITY;
        } elseif($fromAddress === Account::ADDRESS_PILLAR) {
            if($rewardToAccount->is_pillar_withdraw_address) {
                $rewardType = AccountReward::TYPE_PILLAR;
            } else {
                $rewardType = AccountReward::TYPE_DELEGATE;
            }
        }

        if (! $rewardType) {
            return;
        }

        AccountReward::create([
            'account_id' => $rewardToAccount->id,
            'token_id' => $amountBlock->token->id,
            'type' => $rewardType,
            'amount' => $amountBlock->amount,
            'created_at' => $this->block->created_at,
        ]);
    }
}
