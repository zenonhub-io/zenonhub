<?php

declare(strict_types=1);

use App\Actions\Indexer\Bridge\SetNetwork;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\NetworkSet;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\ContractMethod;
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

function createSetNetworkAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => load_account(config('nom.bridge.initialBridgeAdmin')),
        'toAccount' => load_account(EmbeddedContractsEnum::BRIDGE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Bridge', 'SetNetwork'),
        'data' => [
            'networkClass' => '321',
            'chainId' => '1',
            'name' => 'Test',
            'contractAddress' => '0x' . bin2hex(random_bytes(20)),
            'metadata' => "{\"Data\": \"Don't trust. Verify. <3\"}",
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('sets a new bridge network', function () {

    $accountBlock = createSetNetworkAccountBlock();

    SetNetwork::run($accountBlock);

    $network = BridgeNetwork::first();

    expect(BridgeNetwork::get())->toHaveCount(1)
        ->and($network->name)->toEqual($accountBlock->data->decoded['name'])
        ->and($network->network_class)->toEqual($accountBlock->data->decoded['networkClass'])
        ->and($network->chain_identifier)->toEqual($accountBlock->data->decoded['chainId'])
        ->and($network->contract_address)->toEqual($accountBlock->data->decoded['contractAddress'])
        ->and($network->meta_data)->toEqual([
            'Data' => 'Don\'t trust. Verify. <3',
        ]);
});

it('dispatches the network set event', function () {

    $accountBlock = createSetNetworkAccountBlock();

    Event::fake();

    SetNetwork::run($accountBlock);

    Event::assertDispatched(NetworkSet::class);
});

it('ensures only the bridge admin can set a new network', function () {

    $accountBlock = createSetNetworkAccountBlock([
        'account' => Account::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Bridge: SetNetwork failed',
            Mockery::on(fn ($data) => $data['error'] === 'Action sent from non admin')
        )
        ->once();

    SetNetwork::run($accountBlock);

    Event::assertNotDispatched(NetworkSet::class);

    expect(BridgeNetwork::get())->toHaveCount(0);
});

it('ensures only valid chain identifier can be used', function () {

    $accountBlock = createSetNetworkAccountBlock([
        'data' => [
            'networkClass' => '321',
            'chainId' => '0',
            'name' => 'Test',
            'contractAddress' => '0x' . bin2hex(random_bytes(20)),
            'metadata' => '{}',
        ],
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Bridge: SetNetwork failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid networkClass or chainId')
        )
        ->once();

    SetNetwork::run($accountBlock);

    Event::assertNotDispatched(NetworkSet::class);

    expect(BridgeNetwork::get())->toHaveCount(0);
});
