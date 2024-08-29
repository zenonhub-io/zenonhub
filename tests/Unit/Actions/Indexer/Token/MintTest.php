<?php

declare(strict_types=1);

use App\Actions\Indexer\Token\Mint;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Token\TokenMinted;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Token;
use App\Models\Nom\TokenMint;
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

function createMintAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::TOKEN->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (0 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Token', 'Mint'),
        'data' => [
            'tokenStandard' => NetworkTokensEnum::ZNN->value,
            'amount' => (string) (5 * NOM_DECIMALS),
            'receiveAddress' => Account::factory()->create()->address,
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('mints a token', function () {

    $accountBlock = createMintAccountBlock([
        'account' => load_account(EmbeddedContractsEnum::PILLAR->value),
    ]);

    (new Mint)->handle($accountBlock);

    $token = Token::firstWhere('name', 'ZNN');
    $mint = TokenMint::first();

    expect(TokenMint::get())->toHaveCount(1)
        ->and($token->mints()->get())->toHaveCount(1)
        ->and($mint->issuer_id)->toEqual($accountBlock->account_id)
        ->and($mint->receiver->address)->toEqual($accountBlock->data->decoded['receiveAddress'])
        ->and($mint->account_block_id)->toEqual($accountBlock->id)
        ->and($mint->amount)->toEqual($accountBlock->data->decoded['amount']);
});

it('updates a token total supply', function () {

    $token = Token::factory()->create([
        'total_supply' => (string) (1 * NOM_DECIMALS),
        'max_supply' => (string) (10 * NOM_DECIMALS),
    ]);
    $accountBlock = createMintAccountBlock([
        'account' => $token->owner,
        'token' => $token,
        'data' => [
            'tokenStandard' => $token->token_standard,
            'amount' => (string) (1 * NOM_DECIMALS),
            'receiveAddress' => Account::factory()->create()->address,
        ],
    ]);

    (new Mint)->handle($accountBlock);

    $token->refresh();

    $expectedTotalSupply = (string) (2 * NOM_DECIMALS);

    expect($token->total_supply)->toEqual($expectedTotalSupply);
});

it('dispatches the token minted event', function () {

    $accountBlock = createMintAccountBlock([
        'account' => load_account(EmbeddedContractsEnum::PILLAR->value),
    ]);

    Event::fake();

    (new Mint)->handle($accountBlock);

    Event::assertDispatched(TokenMinted::class);
});

it('doesnt pass validation if not mintable', function () {

    $token = Token::factory()->create([
        'is_mintable' => false,
    ]);
    $accountBlock = createMintAccountBlock([
        'data' => [
            'tokenStandard' => $token->token_standard,
            'amount' => (string) (1 * NOM_DECIMALS),
            'receiveAddress' => Account::factory()->create()->address,
        ],
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Token: Mint failed',
            Mockery::on(fn ($data) => $data['error'] === 'Token is not mintable')
        )
        ->once();

    (new Mint)->handle($accountBlock);

    Event::assertNotDispatched(TokenMinted::class);

    expect(TokenMint::get())->toHaveCount(0);
});

it('doesnt pass validation minting more than tne max supply', function () {

    $token = Token::factory()->create([
        'total_supply' => (string) (1 * NOM_DECIMALS),
        'max_supply' => (string) (10 * NOM_DECIMALS),
    ]);
    $accountBlock = createMintAccountBlock([
        'data' => [
            'tokenStandard' => $token->token_standard,
            'amount' => (string) (11 * NOM_DECIMALS),
            'receiveAddress' => Account::factory()->create()->address,
        ],
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Token: Mint failed',
            Mockery::on(fn ($data) => $data['error'] === 'Attempt to mint more than max supply')
        )
        ->once();

    (new Mint)->handle($accountBlock);

    Event::assertNotDispatched(TokenMinted::class);

    expect(TokenMint::get())->toHaveCount(0);
});

it('doesnt pass validation minting network token from non-embedded contract', function () {

    $znn = load_token(NetworkTokensEnum::ZNN->value);
    $accountBlock = createMintAccountBlock([
        'data' => [
            'tokenStandard' => $znn->token_standard,
            'amount' => (string) (1 * NOM_DECIMALS),
            'receiveAddress' => Account::factory()->create()->address,
        ],
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Token: Mint failed',
            Mockery::on(fn ($data) => $data['error'] === 'Normal account trying to mint network owned token')
        )
        ->once();

    (new Mint)->handle($accountBlock);

    Event::assertNotDispatched(TokenMinted::class);

    expect(TokenMint::get())->toHaveCount(0);
});

it('doesnt pass validation minting user tokens from non owner', function () {

    $accountBlock = createMintAccountBlock([
        'data' => [
            'tokenStandard' => Token::factory()->create()->token_standard,
            'amount' => (string) (1 * NOM_DECIMALS),
            'receiveAddress' => Account::factory()->create()->address,
        ],
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Token: Mint failed',
            Mockery::on(fn ($data) => $data['error'] === 'Attempt to mint token by an unauthorized account')
        )
        ->once();

    (new Mint)->handle($accountBlock);

    Event::assertNotDispatched(TokenMinted::class);

    expect(TokenMint::get())->toHaveCount(0);
});
