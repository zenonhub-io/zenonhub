<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Accelerator\AddPhase;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Accelerator\PhaseCreated;
use App\Domains\Indexer\Factories\MockAccountBlockFactory;
use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'accelerator-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createAddPhaseAccountBlock(array $overrides = []): AccountBlock
{
    $project = AcceleratorProject::factory()->accepted()->create();

    $default = [
        'account' => $project->owner,
        'toAccount' => load_account(EmbeddedContractsEnum::ACCELERATOR->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Accelerator', 'AddPhase'),
        'data' => [
            'id' => $project->hash,
            'name' => 'Test Phase',
            'description' => 'Test phase description',
            'url' => 'example.com',
            'znnFundsNeeded' => (string) (5000 * NOM_DECIMALS),
            'qsrFundsNeeded' => (string) (50000 * NOM_DECIMALS),
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('creates a new phase', function () {

    $accountBlock = createAddPhaseAccountBlock();

    (new AddPhase)->handle($accountBlock);

    $project = AcceleratorProject::first();
    $phase = AcceleratorPhase::first();

    expect(AcceleratorPhase::get())->toHaveCount(1)
        ->and($project->phases()->get())->toHaveCount(1)
        ->and($phase->name)->toEqual('Test Phase');
});

it('dispatches the phase created event', function () {

    $accountBlock = createAddPhaseAccountBlock();

    Event::fake();

    (new AddPhase)->handle($accountBlock);

    Event::assertDispatched(PhaseCreated::class);
});

it('ensures only project owner can add phases', function () {

    $accountBlock = createAddPhaseAccountBlock([
        'account' => Account::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Accelerator: AddPhase failed',
            Mockery::on(fn ($data) => $data['error'] === 'Account is not project owner')
        )
        ->once();

    (new AddPhase)->handle($accountBlock);

    Event::assertNotDispatched(PhaseCreated::class);

    expect(AcceleratorPhase::get())->toHaveCount(0);
});

it('ensures phases can only be added to accepted projects', function () {

    $project = AcceleratorProject::factory()
        ->has(AcceleratorPhase::factory()->count(1), 'phases')
        ->accepted()
        ->create();

    $accountBlock = createAddPhaseAccountBlock([
        'account' => $project->owner,
        'data' => [
            'id' => $project->hash,
            'name' => 'Test Phase',
            'description' => 'Test phase description',
            'url' => 'example.com',
            'znnFundsNeeded' => (string) (5000 * NOM_DECIMALS),
            'qsrFundsNeeded' => (string) (50000 * NOM_DECIMALS),
        ],
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Accelerator: AddPhase failed',
            Mockery::on(fn ($data) => $data['error'] === 'Latest phase has not been paid')
        )
        ->once();

    (new AddPhase)->handle($accountBlock);

    Event::assertNotDispatched(PhaseCreated::class);

    expect(AcceleratorPhase::get())->toHaveCount(1);
});
