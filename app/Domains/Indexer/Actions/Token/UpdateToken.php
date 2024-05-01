<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Token;

class UpdateToken extends AbstractIndexerAction
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $this->accountBlock->data->decoded;
        $token = Token::findBy('token_standard', $blockData['tokenStandard']);

        if ($token) {
            $owner = load_account($blockData['owner']);
            $token->owner_id = $owner->id;
            $token->is_burnable = $blockData['isBurnable'];
            $token->is_mintable = $blockData['isMintable'];
            $token->updated_at = $this->accountBlock->created_at;
            $token->save();
        }
    }
}
