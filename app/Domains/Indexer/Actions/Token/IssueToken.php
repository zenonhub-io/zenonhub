<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Token;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Token\TokenIssued;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Token;
use App\Models\NotificationType;
use DigitalSloth\ZnnPhp\Utilities;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class IssueToken extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('token');
        $blockData = $accountBlock->data->decoded;

        if (! $this->validateAction($accountBlock)) {
            Log::info('Contract Method Processor - Token: IssueToken failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        $token = Token::updateOrCreate([
            'token_standard' => Utilities::ztsFromHash($accountBlock->hash),
        ], [
            'chain_id' => $accountBlock->chain_id,
            'owner_id' => $accountBlock->account_id,
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

        Log::info('Contract Method Processor - Token: IssueToken complete', [
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
         */
        [$accountBlock] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if ($blockData['tokenName'] === '' || strlen($blockData['tokenName']) > config('nom.token.nameLengthMax')) {
            return false;
        }

        if ($blockData['tokenSymbol'] === '' || strlen($blockData['tokenSymbol']) > config('nom.token.symbolLengthMax')) {
            return false;
        }

        if ($blockData['tokenDomain'] === '' || strlen($blockData['tokenDomain']) > config('nom.token.domainLengthMax')) {
            return false;
        }

        if (in_array($blockData['tokenSymbol'], ['ZNN', 'QSR'])) {
            return false;
        }

        if ($blockData['decimals'] > config('nom.token.maxDecimals')) {
            return false;
        }

        if (gmp_cmp($blockData['maxSupply'], config('nom.token.maxSupplyBig')) > 0) {
            return false;
        }

        if ($blockData['maxSupply'] <= 0) {
            return false;
        }

        // Total supply is less and equal in case of non-mintable coins
        if (bccomp($blockData['maxSupply'], $blockData['totalSupply']) === -1) {
            return false;
        }

        if (! $blockData['isMintable'] && bccomp($blockData['maxSupply'], $blockData['totalSupply']) !== 0) {
            return false;
        }

        if ($accountBlock->token->token_standard !== NetworkTokensEnum::ZNN->value) {
            return false;
        }

        if ($accountBlock->amount !== config('nom.token.issueAmount')) {
            return false;
        }

        return true;
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
