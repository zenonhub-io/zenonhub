<?php

declare(strict_types=1);

use App\Actions\Indexer\Bridge\RemoveNetwork;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\NetworkRemoved;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\ContractMethod;
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

function createRemoveNetworkAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => load_account(config('nom.bridge.initialBridgeAdmin')),
        'toAccount' => load_account(EmbeddedContractsEnum::BRIDGE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->zts()),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Bridge', 'RemoveNetwork'),
        'data' => [
            'chainId' => '1',
            'networkClass' => '321',
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('removes a bridge network', function () {

    BridgeNetwork::factory()->create([
        'network_class' => '321',
    ]);

    $accountBlock = createRemoveNetworkAccountBlock();

    RemoveNetwork::run($accountBlock);

    expect(BridgeNetwork::get())->toHaveCount(0);
});

it('dispatches the network removed', function () {

    BridgeNetwork::factory()->create([
        'network_class' => '321',
    ]);

    $accountBlock = createRemoveNetworkAccountBlock();

    Event::fake();

    RemoveNetwork::run($accountBlock);

    Event::assertDispatched(NetworkRemoved::class);
});

it('ensures only the bridge admin can remove a network', function () {

    BridgeNetwork::factory()->create([
        'network_class' => '321',
    ]);

    $accountBlock = createRemoveNetworkAccountBlock([
        'account' => Account::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Bridge: RemoveNetwork failed',
            Mockery::on(fn ($data) => $data['error'] === 'Action sent from non admin')
        )
        ->once();

    RemoveNetwork::run($accountBlock);

    Event::assertNotDispatched(NetworkRemoved::class);

    expect(BridgeNetwork::get())->toHaveCount(1);
});
