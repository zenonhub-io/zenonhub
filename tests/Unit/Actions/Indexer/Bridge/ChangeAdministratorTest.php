<?php

declare(strict_types=1);

use App\Actions\Indexer\Bridge\ChangeAdministrator;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\AdministratorChanged;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeAdmin;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\TimeChallenge;
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

function createChangeAdminAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::BRIDGE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Bridge', 'ChangeAdministrator'),
        'data' => [
            'administrator' => Account::factory()->create()->address,
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('changes the bridge admin address', function () {

    $oldAdmin = load_account(config('nom.bridge.initialBridgeAdmin'));
    $accountBlock = createChangeAdminAccountBlock([
        'account' => $oldAdmin,
    ]);

    TimeChallenge::factory()->create([
        'contract_method_id' => ContractMethod::findByContractMethod('Bridge', 'ChangeAdministrator')->id,
        'hash' => Hash::make($accountBlock->data->decoded['administrator']),
        'delay' => config('nom.bridge.minAdministratorDelay'),
        'start_height' => 1,
        'end_height' => 2,
        'created_at' => 2,
    ]);

    ChangeAdministrator::run($accountBlock);

    $newAdmin = BridgeAdmin::getActiveAdmin();

    expect(BridgeAdmin::get())->toHaveCount(2)
        ->and(BridgeAdmin::whereActive()->get())->toHaveCount(1)
        ->and($newAdmin->account->address)->toEqual($accountBlock->data->decoded['administrator']);
});

it('dispatches the admin changed event', function () {

    $oldAdmin = load_account(config('nom.bridge.initialBridgeAdmin'));
    $accountBlock = createChangeAdminAccountBlock([
        'account' => $oldAdmin,
    ]);

    TimeChallenge::factory()->create([
        'contract_method_id' => ContractMethod::findByContractMethod('Bridge', 'ChangeAdministrator')->id,
        'hash' => Hash::make($accountBlock->data->decoded['administrator']),
        'delay' => config('nom.bridge.minAdministratorDelay'),
        'start_height' => 1,
        'end_height' => 2,
    ]);

    Event::fake();

    ChangeAdministrator::run($accountBlock);

    Event::assertDispatched(AdministratorChanged::class);
});

it('ensures only current admin can change bridge admin', function () {

    $accountBlock = createChangeAdminAccountBlock();

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Bridge: ChangeAdministrator failed',
            Mockery::on(fn ($data) => $data['error'] === 'Action sent from non admin')
        )
        ->once();

    ChangeAdministrator::run($accountBlock);

    Event::assertNotDispatched(AdministratorChanged::class);

    expect(BridgeAdmin::get())->toHaveCount(1);
});

it('respects the time challenge', function () {

    $oldAdmin = load_account(config('nom.bridge.initialBridgeAdmin'));
    $accountBlock = createChangeAdminAccountBlock([
        'account' => $oldAdmin,
    ]);

    Event::fake();
    Log::shouldReceive('debug'); // Called in CheckTimeChallenge Action
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Bridge: ChangeAdministrator failed',
            Mockery::on(fn ($data) => $data['error'] === 'Time challenge is still active')
        )
        ->once();

    ChangeAdministrator::run($accountBlock);

    Event::assertNotDispatched(AdministratorChanged::class);

    expect(BridgeAdmin::get())->toHaveCount(1);
});
