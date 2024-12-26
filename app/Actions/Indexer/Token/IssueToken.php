<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Token;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Token\TokenIssued;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Token;
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

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Token: IssueToken failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
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
            'initial_supply' => $blockData['totalSupply'],
            'max_supply' => $blockData['maxSupply'],
            'decimals' => $blockData['decimals'],
            'is_burnable' => $blockData['isBurnable'],
            'is_mintable' => $blockData['isMintable'],
            'is_utility' => $blockData['isUtility'],
            'created_at' => $accountBlock->created_at,
        ]);

        TokenIssued::dispatch($accountBlock, $token);

        Log::error('Contract Method Processor - Token: IssueToken complete', [
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
         */
        [$accountBlock] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if ($blockData['tokenName'] === '' || strlen($blockData['tokenName']) > config('nom.token.nameLengthMax')) {
            throw new IndexerActionValidationException('Invalid token name');
        }

        if ($blockData['tokenSymbol'] === '' || strlen($blockData['tokenSymbol']) > config('nom.token.symbolLengthMax')) {
            throw new IndexerActionValidationException('Invalid token symbol');
        }

        if ($blockData['tokenDomain'] === '' || strlen($blockData['tokenDomain']) > config('nom.token.domainLengthMax')) {
            throw new IndexerActionValidationException('Invalid token domain');
        }

        if (in_array($blockData['tokenSymbol'], ['ZNN', 'QSR'])) {
            throw new IndexerActionValidationException('Token symbol is reserved');
        }

        if ($blockData['decimals'] > config('nom.token.maxDecimals')) {
            throw new IndexerActionValidationException('Too many decimals');
        }

        if (gmp_cmp($blockData['maxSupply'], config('nom.token.maxSupplyBig')) > 0) {
            throw new IndexerActionValidationException('Max supply is too big');
        }

        if ($blockData['maxSupply'] <= 0) {
            throw new IndexerActionValidationException('Max supply is too small');
        }

        // Total supply is less and equal in case of non-mintable coins
        if (bccomp($blockData['maxSupply'], $blockData['totalSupply']) === -1) {
            throw new IndexerActionValidationException('Total supply is less than max supply');
        }

        if (! $blockData['isMintable'] && bccomp($blockData['maxSupply'], $blockData['totalSupply']) !== 0) {
            throw new IndexerActionValidationException('Max and total supply do not match');
        }

        if ($accountBlock->token->token_standard !== NetworkTokensEnum::ZNN->value) {
            throw new IndexerActionValidationException('Send block must be ZNN');
        }

        if ($accountBlock->amount !== config('nom.token.issueAmount')) {
            throw new IndexerActionValidationException('Invalid issue amount');
        }
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
