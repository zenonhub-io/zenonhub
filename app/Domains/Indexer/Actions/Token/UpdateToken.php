<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Token\TokenUpdated;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Token;

class UpdateToken extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $token = Token::findBy('token_standard', $blockData['tokenStandard']);

        if (! $token) {
            return;
        }

        $token->owner_id = load_account($blockData['owner'])->id;
        $token->is_burnable = $blockData['isBurnable'];
        $token->is_mintable = $blockData['isMintable'];
        $token->updated_at = $accountBlock->created_at;
        $token->save();

        TokenUpdated::dispatch($accountBlock, $token);
    }
}
