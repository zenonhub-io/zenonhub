<?php

declare(strict_types=1);

use App\Actions\Indexer\Bridge\WrapToken;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\TokenWrapped;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\BridgeNetworkToken;
use App\Models\Nom\BridgeWrap;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Token;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'bridge-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createWrapTokenAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::BRIDGE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Bridge', 'WrapToken'),
        'data' => [
            'networkClass' => '321',
            'chainId' => '1',
            'toAddress' => '0x' . bin2hex(random_bytes(20)),
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('wraps a token', function () {

    BridgeNetworkToken::factory()->create([
        'bridge_network_id' => BridgeNetwork::factory()->create([
            'network_class' => '321',
        ])->id,
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->id,
        'is_redeemable' => true,
        'is_bridgeable' => true,
    ]);

    $accountBlock = createWrapTokenAccountBlock();

    (new WrapToken)->handle($accountBlock);

    $wrap = BridgeWrap::first();

    expect(BridgeWrap::get())->toHaveCount(1)
        ->and($wrap->account->address)->toEqual($accountBlock->account->address)
        ->and($wrap->amount)->toEqual($accountBlock->amount)
        ->and($wrap->token->token_standard)->toEqual($accountBlock->token->token_standard)
        ->and($wrap->to_address)->toEqual($accountBlock->data->decoded['toAddress']);
});

it('dispatches the token wrapped event', function () {

    BridgeNetworkToken::factory()->create([
        'bridge_network_id' => BridgeNetwork::factory()->create([
            'network_class' => '321',
        ])->id,
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->id,
        'is_redeemable' => true,
        'is_bridgeable' => true,
    ]);

    $accountBlock = createWrapTokenAccountBlock();

    Event::fake();

    (new WrapToken)->handle($accountBlock);

    Event::assertDispatched(TokenWrapped::class);
});

it('ensures wraps only happen on valid bridge networks', function () {

    $accountBlock = createWrapTokenAccountBlock([
        'token' => Token::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: WrapToken failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid bridge network')
        )
        ->once();

    (new WrapToken)->handle($accountBlock);

    Event::assertNotDispatched(TokenWrapped::class);

    expect(BridgeWrap::get())->toHaveCount(0);
});

it('ensures only valid tokens can be bridged', function () {

    BridgeNetworkToken::factory()->create([
        'bridge_network_id' => BridgeNetwork::factory()->create([
            'network_class' => '321',
        ])->id,
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->id,
        'is_redeemable' => true,
        'is_bridgeable' => true,
    ]);

    $accountBlock = createWrapTokenAccountBlock([
        'token' => Token::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: WrapToken failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid token')
        )
        ->once();

    (new WrapToken)->handle($accountBlock);

    Event::assertNotDispatched(TokenWrapped::class);

    expect(BridgeWrap::get())->toHaveCount(0);
});

it('ensures only bridgeable tokens can be wrapped', function () {

    BridgeNetworkToken::factory()->create([
        'bridge_network_id' => BridgeNetwork::factory()->create([
            'network_class' => '321',
        ])->id,
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->id,
        'is_redeemable' => true,
        'is_bridgeable' => false,
    ]);

    $accountBlock = createWrapTokenAccountBlock();

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: WrapToken failed',
            Mockery::on(fn ($data) => $data['error'] === 'Token is not bridgeable')
        )
        ->once();

    (new WrapToken)->handle($accountBlock);

    Event::assertNotDispatched(TokenWrapped::class);

    expect(BridgeWrap::get())->toHaveCount(0);
});
