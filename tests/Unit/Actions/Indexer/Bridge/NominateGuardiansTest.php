<?php

declare(strict_types=1);

use App\Actions\Indexer\Bridge\NominateGuardians;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\GuardiansNominated;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeGuardian;
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

function createNominateGuardiansAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => load_account(config('nom.bridge.initialBridgeAdmin')),
        'toAccount' => load_account(EmbeddedContractsEnum::BRIDGE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Bridge', 'NominateGuardians'),
        'data' => [
            'guardians' => [
                Account::factory()->create()->address,
                Account::factory()->create()->address,
                Account::factory()->create()->address,
                Account::factory()->create()->address,
                Account::factory()->create()->address,
            ],
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('nominates new bridge guardians', function () {

    $accountBlock = createNominateGuardiansAccountBlock();

    TimeChallenge::factory()->create([
        'contract_method_id' => ContractMethod::findByContractMethod('Bridge', 'NominateGuardians')->id,
        'hash' => Hash::make(json_encode($accountBlock->data->decoded['guardians'])),
        'delay' => config('nom.bridge.minSoftDelay'),
        'start_height' => 1,
        'end_height' => 2,
        'created_at' => 2,
    ]);

    (new NominateGuardians)->handle($accountBlock);

    $newGuardians = BridgeGuardian::with('account')->whereActive()->get();

    $newGuardianAddresses = $newGuardians->map(fn (BridgeGuardian $bridgeGuardian) => $bridgeGuardian->account->address)->toArray();

    expect($newGuardians)->toHaveCount(5)
        ->and($newGuardianAddresses)->toEqual($accountBlock->data->decoded['guardians']);
});

it('dispatches the guardians nominated event', function () {

    $accountBlock = createNominateGuardiansAccountBlock();

    TimeChallenge::factory()->create([
        'contract_method_id' => ContractMethod::findByContractMethod('Bridge', 'NominateGuardians')->id,
        'hash' => Hash::make(json_encode($accountBlock->data->decoded['guardians'])),
        'delay' => config('nom.bridge.minSoftDelay'),
        'start_height' => 1,
        'end_height' => 2,
        'created_at' => 2,
    ]);

    Event::fake();

    (new NominateGuardians)->handle($accountBlock);

    Event::assertDispatched(GuardiansNominated::class);
});

it('guardians can only be set from bridge admin account', function () {

    $accountBlock = createNominateGuardiansAccountBlock([
        'account' => Account::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: NominateGuardians failed',
            Mockery::on(fn ($data) => $data['error'] === 'Action sent from non admin')
        )
        ->once();

    (new NominateGuardians)->handle($accountBlock);

    Event::assertNotDispatched(GuardiansNominated::class);

    expect(BridgeGuardian::get())->toHaveCount(0);
});

it('ensures invalid action cannot be processed', function () {

    $accountBlock = createNominateGuardiansAccountBlock([
        'data' => [
            'guardians' => [
                Account::factory()->create()->address,
            ],
        ],
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: NominateGuardians failed',
            Mockery::on(fn ($data) => $data['error'] === 'Not enough guardians nominated')
        )
        ->once();

    (new NominateGuardians)->handle($accountBlock);

    Event::assertNotDispatched(GuardiansNominated::class);

    expect(BridgeGuardian::get())->toHaveCount(0);
});

it('respects the time challenge', function () {

    $accountBlock = createNominateGuardiansAccountBlock();

    Event::fake();
    Log::shouldReceive('debug');
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: NominateGuardians failed',
            Mockery::on(fn ($data) => $data['error'] === 'Time challenge is still active')
        )
        ->once();

    (new NominateGuardians)->handle($accountBlock);

    Event::assertNotDispatched(GuardiansNominated::class);

    expect(BridgeGuardian::get())->toHaveCount(0);
});
