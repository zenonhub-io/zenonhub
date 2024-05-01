<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Token;
use App\Models\NotificationType;
use App\Services\ZenonSdk;
use DigitalSloth\ZnnPhp\Utilities;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;

class IssueToken extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $znn = App::make(ZenonSdk::class);
        $zts = Utilities::ztsFromHash($this->accountBlock->hash);
        $tokenData = $znn->token->getByZts($zts)['data'];
        $token = Token::findBy('token_standard', $tokenData->tokenStandard);
        $totalSupply = $tokenData->totalSupply;
        $maxSupply = $tokenData->maxSupply;

        if (! $token) {
            $token = Token::create([
                'chain_id' => $this->accountBlock->chain->id,
                'owner_id' => $this->accountBlock->account->id,
                'name' => $tokenData->name,
                'symbol' => $tokenData->symbol,
                'domain' => $tokenData->domain,
                'token_standard' => $tokenData->tokenStandard,
                'total_supply' => $totalSupply,
                'max_supply' => $maxSupply,
                'decimals' => $tokenData->decimals,
                'is_burnable' => $tokenData->isBurnable,
                'is_mintable' => $tokenData->isMintable,
                'is_utility' => $tokenData->isUtility,
                'created_at' => $this->accountBlock->created_at,
            ]);
        }

        $token->is_burnable = $tokenData->isBurnable;
        $token->is_mintable = $tokenData->isMintable;
        $token->is_utility = $tokenData->isUtility;
        $token->created_at = $this->accountBlock->created_at;
        $token->save();

        $this->notifyUsers($token);

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
