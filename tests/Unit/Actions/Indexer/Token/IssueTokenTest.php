<?php

declare(strict_types=1);

use App\Actions\Indexer\Token\IssueToken;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Token\TokenIssued;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Token;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'token-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createIssueTokenAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::TOKEN->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Token', 'IssueToken'),
        'data' => [
            'tokenName' => 'Test',
            'tokenSymbol' => 'TEST',
            'tokenDomain' => 'example.com',
            'totalSupply' => '9223372036854775807',
            'maxSupply' => '9223372036854775807',
            'decimals' => '16',
            'isMintable' => true,
            'isBurnable' => true,
            'isUtility' => true,
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('create a new token', function () {

    $accountBlock = createIssueTokenAccountBlock();

    IssueToken::run($accountBlock);

    $token = Token::firstWhere('name', 'Test');

    expect(Token::where('name', 'Test')->get())->toHaveCount(1)
        ->and($token->owner_id)->toEqual($accountBlock->account->id)
        ->and($token->name)->toEqual('Test')
        ->and($token->symbol)->toEqual('TEST')
        ->and($token->total_supply)->toEqual('9223372036854775807')
        ->and($token->max_supply)->toEqual('9223372036854775807')
        ->and($token->decimals)->toEqual(16)
        ->and($token->is_mintable)->toBeTrue()
        ->and($token->is_burnable)->toBeTrue()
        ->and($token->is_utility)->toBeTrue();
});

it('dispatches the token issued event', function () {

    $accountBlock = createIssueTokenAccountBlock();

    Event::fake();

    IssueToken::run($accountBlock);

    Event::assertDispatched(TokenIssued::class);
});

it('doesnt pass validation with reserved symbols', function () {

    $accountBlock = createIssueTokenAccountBlock([
        'data' => [
            'tokenName' => 'ZNN',
            'tokenSymbol' => 'ZNN',
            'tokenDomain' => 'example.com',
            'totalSupply' => '9223372036854775807',
            'maxSupply' => '9223372036854775807',
            'decimals' => '16',
            'isMintable' => true,
            'isBurnable' => true,
            'isUtility' => true,
        ],
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Token: IssueToken failed',
            Mockery::on(fn ($data) => $data['error'] === 'Token symbol is reserved')
        )
        ->once();

    IssueToken::run($accountBlock);

    Event::assertNotDispatched(TokenIssued::class);

    expect(Token::where('name', 'ZNN')->get())->toHaveCount(1);
});
