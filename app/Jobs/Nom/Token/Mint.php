<?php

namespace App\Jobs\Nom\Token;

use App\Actions\SetBlockAsProcessed;
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

    public TokenMint $tokenMint;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        $this->processMint();
        $this->processRewards();
        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function processMint()
    {
        $data = $this->block->data->decoded;
        $account = Utilities::loadAccount($data['receiveAddress']);
        $token = Utilities::loadToken($data['tokenStandard']);

        $this->tokenMint = TokenMint::create([
            'chain_id' => $this->block->chain_id,
            'token_id' => $token->id,
            'issuer_id' => $this->block->account_id,
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

        if ($this->tokenMint->receiver->address === Account::ADDRESS_LIQUIDITY) {
            return;
        }

        $rewardType = null;
        if ($this->block->account->address === Account::ADDRESS_SENTINEL) {
            $rewardType = AccountReward::TYPE_SENTINEL;
        } elseif ($this->block->account->address === Account::ADDRESS_STAKE) {
            $rewardType = AccountReward::TYPE_STAKE;
        } elseif ($this->block->account->address === Account::ADDRESS_LIQUIDITY) {
            $rewardType = AccountReward::TYPE_LIQUIDITY;
        } elseif ($this->block->account->address === Account::ADDRESS_PILLAR) {
            if ($this->tokenMint->receiver->is_pillar_withdraw_address) {
                $rewardType = AccountReward::TYPE_PILLAR;
            } else {
                $rewardType = AccountReward::TYPE_DELEGATE;
            }
        }

        if (! $rewardType) {
            return;
        }

        AccountReward::create([
            'chain_id' => $this->block->chain_id,
            'account_id' => $this->tokenMint->receiver_id,
            'token_id' => $this->tokenMint->token_id,
            'type' => $rewardType,
            'amount' => $this->tokenMint->amount,
            'created_at' => $this->block->created_at,
        ]);
    }
}
