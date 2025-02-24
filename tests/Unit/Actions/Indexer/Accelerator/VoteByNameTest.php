<?php

declare(strict_types=1);

use App\Actions\Indexer\Accelerator\VoteByName;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Enums\Nom\VoteEnum;
use App\Events\Indexer\Accelerator\PillarVoted;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Pillar;
use App\Models\Nom\Vote;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Nom\NetworkSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'accelerator-actions', 'accelerator-vote');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NetworkSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createVoteByNameAccountBlock(array $overrides = []): AccountBlock
{
    $project = AcceleratorProject::factory()->accepted()->create();
    $pillar = Pillar::factory()->create();
    $account = $overrides['account'] ?? $pillar->owner;

    $default = [
        'account' => $account,
        'toAccount' => load_account(EmbeddedContractsEnum::ACCELERATOR->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Accelerator', 'VoteByName'),
        'data' => [
            'id' => $project->hash,
            'name' => $pillar->name,
            'vote' => '0', // Yes vote
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('creates a a new vote', function () {

    $accountBlock = createVoteByNameAccountBlock();

    VoteByName::run($accountBlock);

    $project = AcceleratorProject::first();
    $vote = Vote::first();

    expect(Vote::get())->toHaveCount(1)
        ->and($project->votes()->get())->toHaveCount(1)
        ->and($vote->vote)->toEqual(VoteEnum::YES);
});

it('dispatches the pillar voted event', function () {

    $accountBlock = createVoteByNameAccountBlock();

    Event::fake();

    VoteByName::run($accountBlock);

    Event::assertDispatched(PillarVoted::class);
});

it('ensures only the pillar owner and account block sender account match', function () {

    $accountBlock = createVoteByNameAccountBlock([
        'account' => Account::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Accelerator: VoteByName failed',
            Mockery::on(fn ($data) => $data['error'] === 'Account is not pillar owner')
        )
        ->once();

    VoteByName::run($accountBlock);

    Event::assertNotDispatched(PillarVoted::class);

    expect(AcceleratorPhase::get())->toHaveCount(0);
});
