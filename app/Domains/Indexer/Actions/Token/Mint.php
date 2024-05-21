<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Token\TokenMinted;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\TokenMint;
use Illuminate\Support\Facades\Log;

class Mint extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('account');
        $blockData = $accountBlock->data->decoded;
        $token = load_token($blockData['tokenStandard']);

        if (! $token || ! $this->validateAction($accountBlock, $token)) {
            Log::info('Contract Method Processor - Token: Mint failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

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

        Log::info('Contract Method Processor - Token: Mint complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'mint' => $mint,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): bool
    {
        /**
         * @var AccountBlock $accountBlock
         * @var \App\Domains\Nom\Models\Token $token
         */
        [$accountBlock, $token] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if (! $token->is_mintable) {
            return false;
        }

        if ($blockData['amount'] <= 0) {
            return false;
        }

        // ensure we're not minting more than the max supply
        if (bcsub($token->max_supply, $token->total_supply) < $blockData['amount']) {
            return false;
        }

        if ($token->token_standard === NetworkTokensEnum::ZNN->value && $accountBlock->account->is_embedded_contract) {
            return true;
        }

        if ($token->token_standard === NetworkTokensEnum::QSR->value && $accountBlock->account->is_embedded_contract) {
            return true;
        }

        if ($token->owner_id !== $accountBlock->account_id) {
            return false;
        }

        return true;
    }

    private function updateTokenSupply(TokenMint $mint): void
    {
        $token = $mint->token;
        $token->total_supply += $mint->amount;
        $token->save();
    }
}
