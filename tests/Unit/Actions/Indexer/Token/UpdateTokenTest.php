<?php

declare(strict_types=1);

use App\Actions\Indexer\Token\UpdateToken;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Token\TokenUpdated;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Token;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Nom\NetworkSeeder;
use Database\Seeders\TestGenesisSeeder;
use DigitalSloth\ZnnPhp\Utilities;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'token-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NetworkSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createUpdateTokenAccountBlock(array $overrides = []): AccountBlock
{
    $token = Token::factory()->create([
        'name' => 'Test',
    ]);
    $account = $overrides['account'] ?? $token->owner;

    $default = [
        'account' => $account,
        'toAccount' => load_account(EmbeddedContractsEnum::TOKEN->value),
        'token' => load_token(NetworkTokensEnum::ZNN->zts()),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Token', 'UpdateToken'),
        'data' => [
            'tokenStandard' => $token->token_standard,
            'owner' => $account->address,
            'isMintable' => false,
            'isBurnable' => false,
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('updates an existing token', function () {

    $accountBlock = createUpdateTokenAccountBlock();

    UpdateToken::run($accountBlock);

    $token = Token::firstWhere('name', 'Test');

    expect(Token::where('name', 'Test')->get())->toHaveCount(1)
        ->and($token->owner_id)->toEqual($accountBlock->account_id)
        ->and($token->is_mintable)->toBeFalse()
        ->and($token->is_burnable)->toBeFalse();
});

it('dispatches the token issued event', function () {

    $accountBlock = createUpdateTokenAccountBlock();

    Event::fake();

    UpdateToken::run($accountBlock);

    Event::assertDispatched(TokenUpdated::class);
});

it('doesnt pass validation invalid token standard', function () {

    $account = Account::factory()->create();
    $accountBlock = createUpdateTokenAccountBlock([
        'account' => $account,
        'data' => [
            'tokenStandard' => Utilities::ztsFromHash(fake()->sha256()),
            'owner' => $account->address,
            'isMintable' => false,
            'isBurnable' => false,
        ],
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Token: UpdateToken failed',
            Mockery::on(fn ($data) => $data['error'] === 'No token found')
        )
        ->once();

    UpdateToken::run($accountBlock);

    Event::assertNotDispatched(TokenUpdated::class);
});

it('doesnt pass validation invalid token owner', function () {

    $account = Account::factory()->create();
    $accountBlock = createUpdateTokenAccountBlock([
        'account' => $account,
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Token: UpdateToken failed',
            Mockery::on(fn ($data) => $data['error'] === 'Token owner mismatch')
        )
        ->once();

    UpdateToken::run($accountBlock);

    Event::assertNotDispatched(TokenUpdated::class);
});
