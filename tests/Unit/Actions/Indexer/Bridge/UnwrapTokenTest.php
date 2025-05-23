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
use Database\Seeders\Nom\NetworkSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'bridge-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NetworkSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createUnwrapTokenAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::BRIDGE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->zts()),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Bridge', 'UnwrapToken'),
        'data' => [
            'networkClass' => '321',
            'chainId' => '1',
            'transactionHash' => bin2hex(random_bytes(32)),
            'logIndex' => '74',
            'toAddress' => Account::factory()->create()->address,
            'tokenAddress' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->zts())->token_standard,
            'amount' => '5000000000',
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
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->zts())->id,
        'token_address' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->zts())->token_standard,
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

it('updates total unwrapped and held balances', function () {

    $accountBlockZnn = createUnwrapTokenAccountBlock([
        'token' => load_token(NetworkTokensEnum::ZNN->zts()),
        'data' => [
            'networkClass' => '321',
            'chainId' => '1',
            'transactionHash' => bin2hex(random_bytes(32)),
            'logIndex' => '74',
            'toAddress' => Account::factory()->create()->address,
            'tokenAddress' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->zts())->token_standard,
            'amount' => '5000000000',
            'signature' => 'QPQwxC20ItxXJySGrR+PJMEvv3YbeOaD5tpSAxMAigdtnTT\/WBx6HlCTxExmmFcVZGtH\/misEDRIQ9QyREVqQQA=',
        ],
    ]);

    $accountBlockQsr = createUnwrapTokenAccountBlock([
        'data' => [
            'networkClass' => '321',
            'chainId' => '1',
            'transactionHash' => bin2hex(random_bytes(32)),
            'logIndex' => '74',
            'toAddress' => Account::factory()->create()->address,
            'tokenAddress' => Token::firstWhere('token_standard', NetworkTokensEnum::QSR->zts())->token_standard,
            'amount' => '5000000000',
            'signature' => 'QPQwxC20ItxXJySGrR+PJMEvv3YbeOaD5tpSAxMAigdtnTT\/WBx6HlCTxExmmFcVZGtH\/misEDRIQ9QyREVqQQA=',
        ],
    ]);

    $bridgeNetwork = BridgeNetwork::factory()->create([
        'network_class' => '321',
        'total_znn_wrapped' => '5000000000',
        'total_znn_held' => '5000000000',
        'total_qsr_wrapped' => '5000000000',
        'total_qsr_held' => '5000000000',
    ]);

    BridgeNetworkToken::factory()->create([
        'bridge_network_id' => $bridgeNetwork->id,
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->zts())->id,
        'token_address' => NetworkTokensEnum::ZNN->zts(),
        'is_redeemable' => true,
        'is_bridgeable' => true,
    ]);

    BridgeNetworkToken::factory()->create([
        'bridge_network_id' => $bridgeNetwork->id,
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::QSR->zts())->id,
        'token_address' => NetworkTokensEnum::QSR->zts(),
        'is_redeemable' => true,
        'is_bridgeable' => true,
    ]);

    UnwrapToken::run($accountBlockZnn);
    UnwrapToken::run($accountBlockQsr);

    $bridgeNetwork->refresh();

    expect($bridgeNetwork->total_znn_held)->toEqual(0)
        ->and($bridgeNetwork->total_znn_unwrapped)->toEqual('5000000000')
        ->and($bridgeNetwork->total_qsr_held)->toEqual(0)
        ->and($bridgeNetwork->total_qsr_unwrapped)->toEqual('5000000000');
});

it('dispatches the token unwrapped event', function () {

    BridgeNetworkToken::factory()->create([
        'bridge_network_id' => BridgeNetwork::factory()->create([
            'network_class' => '321',
        ])->id,
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->zts())->id,
        'token_address' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->zts())->token_standard,
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
    Log::shouldReceive('error')
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
    Log::shouldReceive('error')
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
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->zts())->id,
        'token_address' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->zts())->token_standard,
        'is_redeemable' => false,
        'is_bridgeable' => true,
    ]);

    $accountBlock = createUnwrapTokenAccountBlock();

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Bridge: UnwrapToken failed',
            Mockery::on(fn ($data) => $data['error'] === 'Token is not redeemable')
        )
        ->once();

    UnwrapToken::run($accountBlock);

    Event::assertNotDispatched(TokenUnwraped::class);

    expect(BridgeUnwrap::get())->toHaveCount(0);
});
