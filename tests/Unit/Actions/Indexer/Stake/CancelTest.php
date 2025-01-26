<?php

declare(strict_types=1);

use App\Actions\Indexer\Stake\Cancel;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Stake\EndStake;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Stake;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'stake-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createCancelStakeAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::PLASMA->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => '0',
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Plasma', 'CancelFuse'),
        'data' => [
            'id' => hash('sha256', 'example-hash'),
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('cancels a stake', function () {

    $stake = Stake::factory()->create([
        'account_id' => Account::factory()->create(),
        'started_at' => now()->subYear()->subDay(),
    ]);
    $accountBlock = createCancelStakeAccountBlock([
        'account' => $stake->account,
        'data' => [
            'id' => $stake->hash,
        ],
    ]);

    Cancel::run($accountBlock);

    $stake = Stake::first();

    expect(Stake::whereInactive()->get())->toHaveCount(1)
        ->and($stake->ended_at)->toEqual($accountBlock->created_at);
});

it('dispatches the end stake event', function () {

    $stake = Stake::factory()->create([
        'account_id' => Account::factory()->create(),
        'started_at' => now()->subYear()->subDay(),
    ]);
    $accountBlock = createCancelStakeAccountBlock([
        'account' => $stake->account,
        'data' => [
            'id' => $stake->hash,
        ],
    ]);

    Event::fake();

    Cancel::run($accountBlock);

    Event::assertDispatched(EndStake::class);
});

it('ensures only stake owner can cancel stakes', function () {

    $stake = Stake::factory()->create([
        'account_id' => Account::factory()->create(),
        'started_at' => now()->subYear()->subDay(),
    ]);
    $accountBlock = createCancelStakeAccountBlock([
        'data' => [
            'id' => $stake->hash,
        ],
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Stake: Cancel failed',
            Mockery::on(fn ($data) => $data['error'] === 'Account is not stake owner')
        )
        ->once();

    Cancel::run($accountBlock);

    Event::assertNotDispatched(EndStake::class);

    expect(Stake::whereActive()->get())->toHaveCount(1);
});

it('enforces stake duration time', function () {

    $stake = Stake::factory()->create([
        'account_id' => Account::factory()->create(),
        'started_at' => now(),
    ]);
    $accountBlock = createCancelStakeAccountBlock([
        'account' => $stake->account,
        'data' => [
            'id' => $stake->hash,
        ],
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Stake: Cancel failed',
            Mockery::on(fn ($data) => $data['error'] === 'Stake end date in the future')
        )
        ->once();

    Cancel::run($accountBlock);

    Event::assertNotDispatched(EndStake::class);

    expect(Stake::whereActive()->get())->toHaveCount(1);
});
