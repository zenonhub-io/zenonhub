<?php

declare(strict_types=1);

use App\Actions\Indexer\Accelerator\CreateProject;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Accelerator\ProjectCreated;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Token;
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

function createCreateProjectAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::ACCELERATOR->value),
        'token' => load_token(NetworkTokensEnum::ZNN->zts()),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Accelerator', 'CreateProject'),
        'data' => [
            'name' => 'Test',
            'description' => 'Test description',
            'url' => 'example.com',
            'znnFundsNeeded' => (string) (5000 * NOM_DECIMALS),
            'qsrFundsNeeded' => (string) (50000 * NOM_DECIMALS),
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('creates a new project', function () {

    $accountBlock = createCreateProjectAccountBlock();

    CreateProject::run($accountBlock);

    $project = AcceleratorProject::first();

    expect(AcceleratorProject::get())->toHaveCount(1)
        ->and($project->owner_id)->toEqual($accountBlock->account_id)
        ->and($project->name)->toEqual('Test');
});

it('dispatches the project created event', function () {

    $accountBlock = createCreateProjectAccountBlock();

    Event::fake();

    CreateProject::run($accountBlock);

    Event::assertDispatched(ProjectCreated::class);
});

it('ensures the correct fee token is paid', function () {

    $accountBlock = createCreateProjectAccountBlock([
        'token' => Token::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Accelerator: CreateProject failed',
            Mockery::on(fn ($data) => $data['error'] === 'Token fee must be ZNN')
        )
        ->once();

    CreateProject::run($accountBlock);

    Event::assertNotDispatched(ProjectCreated::class);

    expect(AcceleratorProject::get())->toHaveCount(0);
});

it('ensures the correct fee amount is paid', function () {

    $accountBlock = createCreateProjectAccountBlock([
        'amount' => (string) (2 * NOM_DECIMALS),
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Accelerator: CreateProject failed',
            Mockery::on(fn ($data) => $data['error'] === 'Creation fee amount is invalid')
        )
        ->once();

    CreateProject::run($accountBlock);

    Event::assertNotDispatched(ProjectCreated::class);

    expect(AcceleratorProject::get())->toHaveCount(0);
});
