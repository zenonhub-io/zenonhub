<?php

declare(strict_types=1);

use App\Actions\Indexer\Bridge\UnwrapToken;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\TokenUnwraped;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\BridgeNetworkToken;
use App\Models\Nom\BridgeUnwrap;
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

function createUnwrapTokenAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::BRIDGE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Bridge', 'UnwrapToken'),
        'data' => [
            'networkClass' => '321',
            'chainId' => '1',
            'transactionHash' => bin2hex(random_bytes(32)),
            'logIndex' => '74',
            'toAddress' => Account::factory()->create()->address,
            'tokenAddress' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->token_standard,
            'amount' => '485000000',
            'signature' => 'QPQwxC20ItxXJySGrR+PJMEvv3YbeOaD5tpSAxMAigdtnTT\/WBx6HlCTxExmmFcVZGtH\/misEDRIQ9QyREVqQQA=',
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('unwraps a token', function () {

    BridgeNetworkToken::factory()->create([
        'bridge_network_id' => BridgeNetwork::factory()->create([
            'network_class' => '321',
        ])->id,
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->id,
        'token_address' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->token_standard,
        'is_redeemable' => true,
        'is_bridgeable' => true,
    ]);

    $accountBlock = createUnwrapTokenAccountBlock();

    UnwrapToken::run($accountBlock);

    $unwrap = BridgeUnwrap::first();

    expect(BridgeUnwrap::get())->toHaveCount(1)
        ->and($unwrap->toAccount->address)->toEqual($accountBlock->data->decoded['toAddress'])
        ->and($unwrap->amount)->toEqual($accountBlock->data->decoded['amount'])
        ->and($unwrap->token->token_standard)->toEqual($accountBlock->data->decoded['tokenAddress']);
});

it('dispatches the token unwrapped event', function () {

    BridgeNetworkToken::factory()->create([
        'bridge_network_id' => BridgeNetwork::factory()->create([
            'network_class' => '321',
        ])->id,
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->id,
        'token_address' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->token_standard,
        'is_redeemable' => true,
        'is_bridgeable' => true,
    ]);

    $accountBlock = createUnwrapTokenAccountBlock();

    Event::fake();

    UnwrapToken::run($accountBlock);

    Event::assertDispatched(TokenUnwraped::class);
});

it('ensures unwraps only happen on valid bridge networks', function () {

    $accountBlock = createUnwrapTokenAccountBlock([
        'token' => Token::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: UnwrapToken failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid bridge network')
        )
        ->once();

    UnwrapToken::run($accountBlock);

    Event::assertNotDispatched(TokenUnwraped::class);

    expect(BridgeUnwrap::get())->toHaveCount(0);
});

it('ensures only valid tokens can be unwrapped', function () {

    BridgeNetworkToken::factory()->create([
        'bridge_network_id' => BridgeNetwork::factory()->create([
            'network_class' => '321',
        ])->id,
        'is_redeemable' => true,
        'is_bridgeable' => true,
    ]);

    $accountBlock = createUnwrapTokenAccountBlock([
        'token' => Token::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: UnwrapToken failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid token')
        )
        ->once();

    UnwrapToken::run($accountBlock);

    Event::assertNotDispatched(TokenUnwraped::class);

    expect(BridgeUnwrap::get())->toHaveCount(0);
});

it('ensures only redeemable tokens can be unwrapped', function () {

    BridgeNetworkToken::factory()->create([
        'bridge_network_id' => BridgeNetwork::factory()->create([
            'network_class' => '321',
        ])->id,
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->id,
        'token_address' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->token_standard,
        'is_redeemable' => false,
        'is_bridgeable' => true,
    ]);

    $accountBlock = createUnwrapTokenAccountBlock();

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: UnwrapToken failed',
            Mockery::on(fn ($data) => $data['error'] === 'Token is not redeemable')
        )
        ->once();

    UnwrapToken::run($accountBlock);

    Event::assertNotDispatched(TokenUnwraped::class);

    expect(BridgeUnwrap::get())->toHaveCount(0);
});
