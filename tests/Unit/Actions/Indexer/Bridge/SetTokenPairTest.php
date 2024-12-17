<?php

declare(strict_types=1);

use App\Actions\Indexer\Bridge\SetTokenPair;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\TokenPairSet;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\BridgeNetworkToken;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\TimeChallenge;
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

function createSetTokenPaidAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => load_account(config('nom.bridge.initialBridgeAdmin')),
        'toAccount' => load_account(EmbeddedContractsEnum::BRIDGE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Bridge', 'SetTokenPair'),
        'data' => [
            'networkClass' => '321',
            'chainId' => '1',
            'tokenStandard' => Token::factory()->create()->token_standard,
            'tokenAddress' => '0x' . bin2hex(random_bytes(20)),
            'bridgeable' => true,
            'redeemable' => true,
            'owned' => true,
            'minAmount' => '100000000',
            'feePercentage' => '100',
            'redeemDelay' => '90',
            'metadata' => "{\"Data\": \"Don't trust. Verify. <3\"}",
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('sets a new bridge network token', function () {

    BridgeNetwork::factory()->create([
        'network_class' => '321',
    ]);
    $accountBlock = createSetTokenPaidAccountBlock();

    TimeChallenge::factory()->create([
        'contract_method_id' => ContractMethod::findByContractMethod('Bridge', 'SetTokenPair')->id,
        'hash' => Hash::make(json_encode($accountBlock->data->decoded)),
        'delay' => config('nom.bridge.minSoftDelay'),
        'start_height' => 1,
        'end_height' => 2,
        'created_at' => 2,
    ]);

    SetTokenPair::run($accountBlock);

    $networkToken = BridgeNetworkToken::first();

    expect(BridgeNetworkToken::get())->toHaveCount(1)
        ->and($networkToken->token_address)->toEqual($accountBlock->data->decoded['tokenAddress'])
        ->and($networkToken->token->token_standard)->toEqual($accountBlock->data->decoded['tokenStandard'])
        ->and($networkToken->is_bridgeable)->toEqual($accountBlock->data->decoded['bridgeable'])
        ->and($networkToken->is_redeemable)->toEqual($accountBlock->data->decoded['redeemable'])
        ->and($networkToken->is_owned)->toEqual($accountBlock->data->decoded['owned'])
        ->and($networkToken->min_amount)->toEqual($accountBlock->data->decoded['minAmount'])
        ->and($networkToken->fee_percentage)->toEqual($accountBlock->data->decoded['feePercentage'])
        ->and($networkToken->metadata)->toEqual([
            'Data' => 'Don\'t trust. Verify. <3',
        ]);
});

it('dispatches the network set event', function () {

    BridgeNetwork::factory()->create([
        'network_class' => '321',
    ]);
    $accountBlock = createSetTokenPaidAccountBlock();

    TimeChallenge::factory()->create([
        'contract_method_id' => ContractMethod::findByContractMethod('Bridge', 'SetTokenPair')->id,
        'hash' => Hash::make(json_encode($accountBlock->data->decoded)),
        'delay' => config('nom.bridge.minSoftDelay'),
        'start_height' => 1,
        'end_height' => 2,
        'created_at' => 2,
    ]);

    Event::fake();

    SetTokenPair::run($accountBlock);

    Event::assertDispatched(TokenPairSet::class);
});

it('ensures only the bridge admin can set a new network', function () {

    BridgeNetwork::factory()->create([
        'network_class' => '321',
    ]);
    $accountBlock = createSetTokenPaidAccountBlock([
        'account' => Account::factory()->create(),
    ]);

    TimeChallenge::factory()->create([
        'contract_method_id' => ContractMethod::findByContractMethod('Bridge', 'SetTokenPair')->id,
        'hash' => Hash::make(json_encode($accountBlock->data->decoded)),
        'delay' => config('nom.bridge.minSoftDelay'),
        'start_height' => 1,
        'end_height' => 2,
        'created_at' => 2,
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: SetTokenPair failed',
            Mockery::on(fn ($data) => $data['error'] === 'Action sent from non admin')
        )
        ->once();

    SetTokenPair::run($accountBlock);

    Event::assertNotDispatched(TokenPairSet::class);

    expect(BridgeNetworkToken::get())->toHaveCount(0);
});

it('ensures only valid tokens can be added', function () {

    BridgeNetwork::factory()->create([
        'network_class' => '321',
    ]);
    $accountBlock = createSetTokenPaidAccountBlock([
        'data' => [
            'networkClass' => '321',
            'chainId' => '1',
            'tokenStandard' => Token::factory()->make()->token_standard,
            'tokenAddress' => '0x' . bin2hex(random_bytes(20)),
            'bridgeable' => true,
            'redeemable' => true,
            'owned' => true,
            'minAmount' => '100000000',
            'feePercentage' => '100',
            'redeemDelay' => '90',
            'metadata' => "{\"Data\": \"Don't trust. Verify. <3\"}",
        ],
    ]);

    TimeChallenge::factory()->create([
        'contract_method_id' => ContractMethod::findByContractMethod('Bridge', 'SetTokenPair')->id,
        'hash' => Hash::make(json_encode($accountBlock->data->decoded)),
        'delay' => config('nom.bridge.minSoftDelay'),
        'start_height' => 1,
        'end_height' => 2,
        'created_at' => 2,
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: SetTokenPair failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid token')
        )
        ->once();

    SetTokenPair::run($accountBlock);

    Event::assertNotDispatched(TokenPairSet::class);

    expect(BridgeNetworkToken::get())->toHaveCount(0);
});

it('ensures the time challenge is respected', function () {

    BridgeNetwork::factory()->create([
        'network_class' => '321',
    ]);
    $accountBlock = createSetTokenPaidAccountBlock();

    Event::fake();
    Log::shouldReceive('debug');
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: SetTokenPair failed',
            Mockery::on(fn ($data) => $data['error'] === 'Time challenge is still active')
        )
        ->once();

    SetTokenPair::run($accountBlock);

    Event::assertNotDispatched(TokenPairSet::class);

    expect(BridgeNetworkToken::get())->toHaveCount(0);
});
