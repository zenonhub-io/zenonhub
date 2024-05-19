<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Token\TokenUpdated;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Token;
use Illuminate\Support\Facades\Log;

class UpdateToken extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $token = Token::findBy('token_standard', $blockData['tokenStandard']);

        if (! $token || ! $this->validateAction($accountBlock, $token)) {
            Log::info('Contract Method Processor - Token: UpdateToken failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
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

    protected function validateAction(): bool
    {
        /**
         * @var AccountBlock $accountBlock
         * @var Token $token
         */
        [$accountBlock, $token] = func_get_args();

        if ($token->owner_id !== $accountBlock->account_id) {
            return false;
        }

        return true;
    }
}
