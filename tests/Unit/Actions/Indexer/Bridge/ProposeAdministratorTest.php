<?php

declare(strict_types=1);

use App\Actions\Indexer\Bridge\ProposeAdministrator;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\AdministratorProposed;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeAdmin;
use App\Models\Nom\BridgeGuardian;
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

function createProposeAdminAccountBlock(array $overrides = []): AccountBlock
{
    $guardian = BridgeGuardian::factory()->accepted()->create();

    $default = [
        'account' => $guardian->account,
        'toAccount' => load_account(EmbeddedContractsEnum::BRIDGE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Bridge', 'ProposeAdministrator'),
        'data' => [
            'address' => Account::factory()->create()->address,
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('creates a admin proposal', function () {

    BridgeGuardian::factory()->count(4)->accepted()->create();

    $accountBlock = createProposeAdminAccountBlock();

    (new ProposeAdministrator)->handle($accountBlock);

    expect(BridgeAdmin::whereProposed()->get())->toHaveCount(1)
        ->and(BridgeAdmin::getActiveAdmin()->account->address)->toEqual(config('nom.bridge.initialBridgeAdmin'))
        ->and(BridgeAdmin::whereProposed()->first()->account->address)->toEqual($accountBlock->data->decoded['address']);
});

it('changes the active admin if more than half the guardians vote for the same address', function () {

    $accountBlock = createProposeAdminAccountBlock();

    (new ProposeAdministrator)->handle($accountBlock);

    expect(BridgeAdmin::whereActive()->get())->toHaveCount(1)
        ->and(BridgeAdmin::getActiveAdmin()->account->address)->toEqual($accountBlock->data->decoded['address']);
});

it('dispatches the admin proposed event', function () {

    $accountBlock = createProposeAdminAccountBlock();

    Event::fake();

    (new ProposeAdministrator)->handle($accountBlock);

    Event::assertDispatched(AdministratorProposed::class);
});

it('ensures only guardians can propose admins', function () {

    $accountBlock = createProposeAdminAccountBlock([
        'account' => Account::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: ProposeAdministrator failed',
            Mockery::on(fn ($data) => $data['error'] === 'Action sent from non guardian')
        )
        ->once();

    (new ProposeAdministrator)->handle($accountBlock);

    Event::assertNotDispatched(AdministratorProposed::class);

    expect(BridgeAdmin::getActiveAdmin()->account->address)->toEqual(config('nom.bridge.initialBridgeAdmin'));
});
