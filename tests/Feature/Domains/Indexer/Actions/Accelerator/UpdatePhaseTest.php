<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Accelerator\UpdatePhase;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Accelerator\PhaseUpdated;
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

function createUpdatePhaseAccountBlock(array $overrides = []): AccountBlock
{
    $project = AcceleratorProject::factory()
        ->has(AcceleratorPhase::factory()->count(1), 'phases')
        ->accepted()
        ->create();

    $default = [
        'account' => $project->owner,
        'toAccount' => load_account(EmbeddedContractsEnum::ACCELERATOR->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
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

    (new UpdatePhase)->handle($accountBlock);

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

    (new UpdatePhase)->handle($accountBlock);

    Event::assertDispatched(PhaseUpdated::class);
});

it('ensures only project owner can update phase', function () {

    $accountBlock = createUpdatePhaseAccountBlock([
        'account' => Account::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Accelerator: UpdatePhase failed',
            Mockery::on(fn ($data) => $data['error'] === 'Account is not project owner')
        )
        ->once();

    (new UpdatePhase)->handle($accountBlock);

    Event::assertNotDispatched(PhaseUpdated::class);

    $phase = AcceleratorPhase::first();

    expect(AcceleratorPhase::get())->toHaveCount(1)
        ->and($phase->name)->not->toEqual('Updated Phase');
});

it('ensures latest phase is open', function () {

    $accountBlock = createUpdatePhaseAccountBlock();
    $project = AcceleratorProject::first();
    $project->phases()->update([
        'status' => App\Domains\Nom\Enums\AcceleratorPhaseStatusEnum::PAID->value,
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Accelerator: UpdatePhase failed',
            Mockery::on(fn ($data) => $data['error'] === 'Latest phase is not open')
        )
        ->once();

    (new UpdatePhase)->handle($accountBlock);

    Event::assertNotDispatched(PhaseUpdated::class);

    expect(AcceleratorPhase::get())->toHaveCount(1);
});
