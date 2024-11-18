<?php

declare(strict_types=1);

use App\Actions\Indexer\Bridge\SetNetworkMetadata;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\NetworkMetadataSet;
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

function createSetNetworkMetadataAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => load_account(config('nom.bridge.initialBridgeAdmin')),
        'toAccount' => load_account(EmbeddedContractsEnum::BRIDGE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Bridge', 'SetNetworkMetadata'),
        'data' => [
            'networkClass' => '321',
            'chainId' => '1',
            'metadata' => "{\"Data\": \"Don't trust. Verify. <3\"}",
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('updates a networks metadata', function () {

    BridgeNetwork::factory()->create([
        'network_class' => '321',
    ]);

    $accountBlock = createSetNetworkMetadataAccountBlock();

    SetNetworkMetadata::run($accountBlock);

    expect(BridgeNetwork::first()->meta_data)->toEqual([
        'Data' => 'Don\'t trust. Verify. <3',
    ]);
});

it('dispatches the network metadata set event', function () {

    BridgeNetwork::factory()->create([
        'network_class' => '321',
    ]);

    $accountBlock = createSetNetworkMetadataAccountBlock();

    Event::fake();

    SetNetworkMetadata::run($accountBlock);

    Event::assertDispatched(NetworkMetadataSet::class);
});

it('ensures only admin can set the network metadata', function () {

    BridgeNetwork::factory()->create([
        'network_class' => '321',
    ]);

    $accountBlock = createSetNetworkMetadataAccountBlock([
        'account' => Account::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: SetNetworkMetadata failed',
            Mockery::on(fn ($data) => $data['error'] === 'Action sent from non admin')
        )
        ->once();

    SetNetworkMetadata::run($accountBlock);

    Event::assertNotDispatched(NetworkMetadataSet::class);

    expect(BridgeNetwork::first()->meta_data)->toBeEmpty();
});
