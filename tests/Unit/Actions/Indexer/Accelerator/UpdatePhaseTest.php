<?php

declare(strict_types=1);

use App\Actions\Indexer\Accelerator\UpdatePhase;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Accelerator\PhaseUpdated;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Nom\NetworkSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'accelerator-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NetworkSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createUpdatePhaseAccountBlock(array $overrides = []): AccountBlock
{
    $project = AcceleratorProject::factory()
        ->has(AcceleratorPhase::factory()->count(1), 'phases')
        ->accepted()
        ->create();

    $default = [
        'account' => $project->owner,
        'toAccount' => load_account(EmbeddedContractsEnum::ACCELERATOR->value),
        'token' => load_token(NetworkTokensEnum::ZNN->zts()),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Accelerator', 'UpdatePhase'),
        'data' => [
            'id' => $project->hash,
            'name' => 'Updated Phase',
            'description' => 'Test phase description updated',
            'url' => 'example.com',
            'znnFundsNeeded' => (string) (500 * NOM_DECIMALS),
            'qsrFundsNeeded' => (string) (5000 * NOM_DECIMALS),
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('updates an existing phase', function () {

    $accountBlock = createUpdatePhaseAccountBlock();

    UpdatePhase::run($accountBlock);

    $project = AcceleratorProject::first();
    $phase = AcceleratorPhase::first();

    expect(AcceleratorPhase::get())->toHaveCount(1)
        ->and($phase->votes()->get())->toHaveCount(0)
        ->and($project->phases()->get())->toHaveCount(1)
        ->and($project->updated_at)->toEqual($accountBlock->created_at)
        ->and($phase->name)->toEqual('Updated Phase')
        ->and($phase->description)->toEqual('Test phase description updated')
        ->and($phase->znn_requested)->toEqual((string) (500 * NOM_DECIMALS))
        ->and($phase->qsr_requested)->toEqual((string) (5000 * NOM_DECIMALS));
});

it('dispatches the phase updated event', function () {

    $accountBlock = createUpdatePhaseAccountBlock();

    Event::fake();

    UpdatePhase::run($accountBlock);

    Event::assertDispatched(PhaseUpdated::class);
});

it('ensures only project owner can update phase', function () {

    $accountBlock = createUpdatePhaseAccountBlock([
        'account' => Account::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Accelerator: UpdatePhase failed',
            Mockery::on(fn ($data) => $data['error'] === 'Account is not project owner')
        )
        ->once();

    UpdatePhase::run($accountBlock);

    Event::assertNotDispatched(PhaseUpdated::class);

    $phase = AcceleratorPhase::first();

    expect(AcceleratorPhase::get())->toHaveCount(1)
        ->and($phase->name)->not->toEqual('Updated Phase');
});

it('ensures latest phase is open', function () {

    $accountBlock = createUpdatePhaseAccountBlock();
    $project = AcceleratorProject::first();
    $project->phases()->update([
        'status' => App\Enums\Nom\AcceleratorPhaseStatusEnum::PAID->value,
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Accelerator: UpdatePhase failed',
            Mockery::on(fn ($data) => $data['error'] === 'Latest phase is not open')
        )
        ->once();

    UpdatePhase::run($accountBlock);

    Event::assertNotDispatched(PhaseUpdated::class);

    expect(AcceleratorPhase::get())->toHaveCount(1);
});
