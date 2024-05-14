<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Token\TokenIssued;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Token;
use App\Models\NotificationType;
use DigitalSloth\ZnnPhp\Utilities;
use Illuminate\Support\Facades\Notification;

class IssueToken extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $zts = Utilities::ztsFromHash($accountBlock->hash);

        $token = Token::updateOrCreate([
            'token_standard' => $zts,
        ], [
            'chain_id' => $accountBlock->chain->id,
            'owner_id' => $accountBlock->account->id,
            'name' => $blockData['tokenName'],
            'symbol' => $blockData['tokenSymbol'],
            'domain' => $blockData['tokenDomain'],
            'total_supply' => $blockData['totalSupply'],
            'max_supply' => $blockData['maxSupply'],
            'decimals' => $blockData['decimals'],
            'is_burnable' => $blockData['isBurnable'],
            'is_mintable' => $blockData['isMintable'],
            'is_utility' => $blockData['isUtility'],
            'created_at' => $accountBlock->created_at,
        ]);

        TokenIssued::dispatch($accountBlock, $token);
    }

    private function notifyUsers($token): void
    {
        $subscribedUsers = NotificationType::getSubscribedUsers('network-token');
        $networkBot = new \App\Bots\NetworkAlertBot;

        Notification::send(
            $subscribedUsers->prepend($networkBot),
            new \App\Notifications\Nom\Token\Issued($token)
        );
    }
}
