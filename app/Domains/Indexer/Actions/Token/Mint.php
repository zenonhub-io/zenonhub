<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Enums\AccountRewardTypesEnum;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\AccountReward;
use App\Domains\Nom\Models\TokenMint;

class Mint extends AbstractContractMethodProcessor
{
    public TokenMint $tokenMint;

    public function handle(AccountBlock $accountBlock): void
    {
        $this->processMint();
        $this->processRewards();
        $this->updateTokenSupply();

    }

    private function processMint()
    {
        $data = $this->accountBlock->data->decoded;
        $account = load_account($data['receiveAddress']);
        $token = load_token($data['tokenStandard']);

        $this->tokenMint = TokenMint::create([
            'chain_id' => $this->accountBlock->chain_id,
            'token_id' => $token->id,
            'issuer_id' => $this->accountBlock->account_id,
            'receiver_id' => $account->id,
            'account_block_id' => $this->accountBlock->id,
            'amount' => $data['amount'],
            'created_at' => $this->accountBlock->created_at,
        ]);
    }

    private function processRewards()
    {
        if (! $this->accountBlock->parent) {
            return;
        }

        if ($this->tokenMint->receiver->address === EmbeddedContractsEnum::LIQUIDITY->value) {
            return;
        }

        $rewardType = null;
        if ($this->accountBlock->account->address === EmbeddedContractsEnum::SENTINEL->value) {
            $rewardType = AccountRewardTypesEnum::SENTINEL->value;
        } elseif ($this->accountBlock->account->address === EmbeddedContractsEnum::STAKE->value) {
            $rewardType = AccountRewardTypesEnum::STAKE->value;
        } elseif ($this->accountBlock->account->address === EmbeddedContractsEnum::LIQUIDITY->value) {
            $rewardType = AccountRewardTypesEnum::LIQUIDITY->value;
        } elseif ($this->accountBlock->account->address === EmbeddedContractsEnum::PILLAR->value) {
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
            'chain_id' => $this->accountBlock->chain_id,
            'account_id' => $this->tokenMint->receiver_id,
            'token_id' => $this->tokenMint->token_id,
            'type' => $rewardType,
            'amount' => $this->tokenMint->amount,
            'created_at' => $this->accountBlock->created_at,
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
