<?php

declare(strict_types=1);

use App\Actions\Indexer\Token\Burn;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Token\TokenBurned;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Token;
use App\Models\Nom\TokenBurn;
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

function createBurnAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::TOKEN->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (5 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Token', 'Burn'),
        'data' => '',
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('burns a token', function () {

    $accountBlock = createBurnAccountBlock();

    Burn::run($accountBlock);

    $token = Token::firstWhere('name', 'ZNN');
    $burn = TokenBurn::first();

    expect(TokenBurn::get())->toHaveCount(1)
        ->and($token->burns()->get())->toHaveCount(1)
        ->and($burn->amount)->toEqual($accountBlock->amount)
        ->and($burn->account_id)->toEqual($accountBlock->account_id)
        ->and($burn->account_block_id)->toEqual($accountBlock->id);
});

it('burns a token if the sender is the owner', function () {

    $token = Token::factory()->create([
        'is_burnable' => false,
    ]);

    $accountBlock = createBurnAccountBlock([
        'account' => $token->owner,
        'token' => $token,
    ]);

    Burn::run($accountBlock);

    $burn = TokenBurn::first();

    expect(TokenBurn::get())->toHaveCount(1)
        ->and($token->burns()->get())->toHaveCount(1)
        ->and($burn->amount)->toEqual($accountBlock->amount)
        ->and($burn->account_id)->toEqual($accountBlock->account_id)
        ->and($burn->account_block_id)->toEqual($accountBlock->id);
});

it('updates a token total supply', function () {

    $token = Token::factory()->create([
        'total_supply' => (string) (10 * NOM_DECIMALS),
        'max_supply' => (string) (10 * NOM_DECIMALS),
    ]);
    $accountBlock = createBurnAccountBlock([
        'account' => $token->owner,
        'token' => $token,
        'amount' => (string) (1 * NOM_DECIMALS),
    ]);

    Burn::run($accountBlock);

    $token->refresh();

    $expectedTotalSupply = (string) (9 * NOM_DECIMALS);

    expect($token->total_supply)->toEqual($expectedTotalSupply);
});

it('updates a token max supply', function () {

    $token = Token::factory()->create([
        'is_mintable' => false,
        'total_supply' => (string) (10 * NOM_DECIMALS),
        'max_supply' => (string) (10 * NOM_DECIMALS),
    ]);
    $accountBlock = createBurnAccountBlock([
        'account' => $token->owner,
        'token' => $token,
        'amount' => (string) (1 * NOM_DECIMALS),
    ]);

    Burn::run($accountBlock);

    $token->refresh();

    $expectedMaxSupply = (string) (9 * NOM_DECIMALS);

    expect($token->max_supply)->toEqual($expectedMaxSupply);
});

it('dispatches the token burned event', function () {

    $accountBlock = createBurnAccountBlock();

    Event::fake();

    Burn::run($accountBlock);

    Event::assertDispatched(TokenBurned::class);
});

it('doesnt pass validation if not burnable', function () {

    $token = Token::factory()->create([
        'is_burnable' => false,
    ]);
    $accountBlock = createBurnAccountBlock([
        'token' => $token,
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Token: Burn failed',
            Mockery::on(fn ($data) => $data['error'] === 'Token is not burnable, or owner doesnt match')
        )
        ->once();

    Burn::run($accountBlock);

    Event::assertNotDispatched(TokenBurned::class);

    expect(TokenBurn::get())->toHaveCount(0);
});
