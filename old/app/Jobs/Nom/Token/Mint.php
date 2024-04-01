<?php

declare(strict_types=1);

namespace App\Jobs\Nom\Token;

use App\Actions\SetBlockAsProcessed;
use App\Domains\Nom\Enums\AccountRewardTypesEnum;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\AccountReward;
use App\Domains\Nom\Models\TokenMint;
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
        $this->updateTokenSupply();
        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function processMint()
    {
        $data = $this->block->data->decoded;
        $account = load_account($data['receiveAddress']);
        $token = load_token($data['tokenStandard']);

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

        if ($this->tokenMint->receiver->address === EmbeddedContractsEnum::LIQUIDITY->value) {
            return;
        }

        $rewardType = null;
        if ($this->block->account->address === EmbeddedContractsEnum::SENTINEL->value) {
            $rewardType = AccountRewardTypesEnum::SENTINEL->value;
        } elseif ($this->block->account->address === EmbeddedContractsEnum::STAKE->value) {
            $rewardType = AccountRewardTypesEnum::STAKE->value;
        } elseif ($this->block->account->address === EmbeddedContractsEnum::LIQUIDITY->value) {
            $rewardType = AccountRewardTypesEnum::LIQUIDITY->value;
        } elseif ($this->block->account->address === EmbeddedContractsEnum::PILLAR->value) {
            if ($this->tokenMint->receiver->is_pillar_withdraw_address) {
                $rewardType = AccountRewardTypesEnum::PILLAR->value;
            } else {
                $rewardType = AccountRewardTypesEnum::DELEGATE->value;
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

    private function updateTokenSupply()
    {
        $token = $this->tokenMint->token;
        $data = $token->raw_json;
        $token->total_supply = $data->totalSupply;
        $token->max_supply = $data->maxSupply;
        $token->save();
    }
}
