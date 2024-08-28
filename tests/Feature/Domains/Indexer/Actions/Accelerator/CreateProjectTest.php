<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Accelerator\CreateProject;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Accelerator\ProjectCreated;
use App\Domains\Indexer\Factories\MockAccountBlockFactory;
use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Token;
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

function createCreateProjectAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::ACCELERATOR->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
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

it('cancels a new project', function () {

    $accountBlock = createCreateProjectAccountBlock();

    (new CreateProject)->handle($accountBlock);

    $project = AcceleratorProject::first();

    expect(AcceleratorProject::get())->toHaveCount(1)
        ->and($project->owner_id)->toEqual($accountBlock->account_id)
        ->and($project->name)->toEqual('Test');
});

it('dispatches the project created event', function () {

    $accountBlock = createCreateProjectAccountBlock();

    Event::fake();

    (new CreateProject)->handle($accountBlock);

    Event::assertDispatched(ProjectCreated::class);
});

it('ensures the correct fee token is paid', function () {

    $accountBlock = createCreateProjectAccountBlock([
        'token' => Token::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Accelerator: CreateProject failed',
            Mockery::on(fn ($data) => $data['error'] === 'Token fee must be ZNN')
        )
        ->once();

    (new CreateProject)->handle($accountBlock);

    Event::assertNotDispatched(ProjectCreated::class);

    expect(AcceleratorProject::get())->toHaveCount(0);
});

it('ensures the correct fee amount is paid', function () {

    $accountBlock = createCreateProjectAccountBlock([
        'amount' => (string) (2 * NOM_DECIMALS),
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Accelerator: CreateProject failed',
            Mockery::on(fn ($data) => $data['error'] === 'Creation fee amount is invalid')
        )
        ->once();

    (new CreateProject)->handle($accountBlock);

    Event::assertNotDispatched(ProjectCreated::class);

    expect(AcceleratorProject::get())->toHaveCount(0);
});
