<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Token\TokenUpdated;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Token;
use Illuminate\Support\Facades\Log;

class UpdateToken extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $token = Token::firstWhere('token_standard', $blockData['tokenStandard']);

        try {
            $this->validateAction($accountBlock, $token);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Token: UpdateToken failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $token->owner_id = load_account($blockData['owner'])->id;
        $token->is_burnable = $blockData['isBurnable'];
        $token->is_mintable = $blockData['isMintable'];
        $token->updated_at = $accountBlock->created_at;
        $token->save();

        TokenUpdated::dispatch($accountBlock, $token);

        Log::info('Contract Method Processor - Token: UpdateToken complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'token' => $token,
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
         * @var Token $token
         */
        [$accountBlock, $token] = func_get_args();

        if (! $token) {
            throw new IndexerActionValidationException('No token found');
        }

        if ($token->owner_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Token owner mismatch');
        }
    }
}
