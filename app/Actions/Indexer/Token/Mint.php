<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Token;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Token\TokenMinted;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\TokenMint;
use Illuminate\Support\Facades\Log;

class Mint extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('account');
        $blockData = $accountBlock->data->decoded;
        $token = load_token($blockData['tokenStandard']);

        try {
            $this->validateAction($accountBlock, $token);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Token: Mint failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
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

        $token = $mint->token;
        $token->total_supply += $mint->amount;
        $token->save();

        TokenMinted::dispatch($accountBlock, $mint);

        Log::info('Contract Method Processor - Token: Mint complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'mint' => $mint,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    /**
     * @throws IndexerActionValidationException
     */
    public function validateAction(): void
    {
        /**
         * @var AccountBlock $accountBlock
         * @var \App\Models\Nom\Token $token
         */
        [$accountBlock, $token] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if (! $token) {
            throw new IndexerActionValidationException('No token found');
        }

        if (! $token->is_mintable) {
            throw new IndexerActionValidationException('Token is not mintable');
        }

        if ($blockData['amount'] <= 0) {
            throw new IndexerActionValidationException('Amount is less then or equal to 0');
        }

        // ensure we're not minting more than the max supply
        if (bcsub($token->max_supply, $token->total_supply) < $blockData['amount']) {
            throw new IndexerActionValidationException('Attempt to mint more than max supply');
        }

        $networkMintableTokens = [NetworkTokensEnum::ZNN->zts(), NetworkTokensEnum::QSR->zts()];

        if (in_array($token->token_standard, $networkMintableTokens, true)) {
            if (! $accountBlock->account->is_embedded_contract) {
                throw new IndexerActionValidationException('Normal account trying to mint network owned token');
            }
        } elseif ($token->owner_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Attempt to mint token by an unauthorized account');
        }
    }
}
