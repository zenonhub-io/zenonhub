<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Token\TokenMinted;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\TokenMint;

class Mint extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $this->accountBlock = $accountBlock;
        $blockData = $accountBlock->data->decoded;
        $token = load_token($blockData['tokenStandard']);

        if (! $token || ! $this->validateAction($token)) {
            return;
        }

        $mint = TokenMint::create([
            'chain_id' => $accountBlock->chain_id,
            'token_id' => load_token($blockData['tokenStandard'])->id,
            'issuer_id' => $accountBlock->account_id,
            'receiver_id' => load_account($blockData['receiveAddress'])->id,
            'account_block_id' => $accountBlock->id,
            'amount' => $blockData['amount'],
            'created_at' => $accountBlock->created_at,
        ]);

        $this->updateTokenSupply($mint);

        TokenMinted::dispatch($accountBlock, $mint);
    }

    protected function validateAction(): bool
    {
        [$token] = func_get_args();

        if (! $token->is_mintable) {
            return false;
        }

        if ($this->accountBlock->data->decoded['amount'] <= 0) {
            return false;
        }

        if ($token->token_standard === NetworkTokensEnum::ZNN->value && $this->accountBlock->account->is_embedded_contract) {
            return true;
        }

        if ($token->token_standard === NetworkTokensEnum::QSR->value && $this->accountBlock->account->is_embedded_contract) {
            return true;
        }

        return $token->owner_id === $this->accountBlock->account_id;
    }

    private function updateTokenSupply(TokenMint $mint): void
    {
        $token = $mint->token;
        $token->total_supply += $mint->amount;
        $token->save();
    }
}
