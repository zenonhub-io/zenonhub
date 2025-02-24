<?php

declare(strict_types=1);

use App\Actions\Indexer\Stake\Stake as StakeAction;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Stake\StartStake;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Stake;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Nom\NetworkSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'stake-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NetworkSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createStakeAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::STAKE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (100 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Stake', 'Stake'),
        'data' => [
            'durationInSec' => '31104000',
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('create a new stake', function () {

    $accountBlock = createStakeAccountBlock();

    StakeAction::run($accountBlock);

    $stake = Stake::first();

    expect(Stake::whereActive()->get())->toHaveCount(1)
        ->and($stake->account_id)->toEqual($accountBlock->account->id)
        ->and($stake->amount)->toEqual($accountBlock->amount)
        ->and($stake->duration)->toEqual('31104000')
        ->and($stake->started_at)->toEqual($accountBlock->created_at)
        ->and($stake->ended_at)->toBeNull();
});

it('dispatches the start stake event', function () {

    $accountBlock = createStakeAccountBlock();

    Event::fake();

    StakeAction::run($accountBlock);

    Event::assertDispatched(StartStake::class);
});

it('doesnt pass validation with invalid token', function () {

    $accountBlock = createStakeAccountBlock([
        'token' => load_token(NetworkTokensEnum::QSR->value),
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Stake: Stake failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid stake token')
        )
        ->once();

    StakeAction::run($accountBlock);

    Event::assertNotDispatched(StartStake::class);

    expect(Stake::get())->toHaveCount(0);
});

it('doesnt pass validation with invalid amount of ZNN', function () {

    $accountBlock = createStakeAccountBlock([
        'amount' => '1',
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Stake: Stake failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid stake amount')
        )
        ->once();

    StakeAction::run($accountBlock);

    Event::assertNotDispatched(StartStake::class);

    expect(Stake::whereActive()->get())->toHaveCount(0);
});

it('doesnt pass validation with short duration', function () {

    $duration = config('nom.stake.timeMinSec') - 1;
    $accountBlock = createStakeAccountBlock([
        'data' => [
            'durationInSec' => $duration,
        ],
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Stake: Stake failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid stake duration')
        )
        ->once();

    StakeAction::run($accountBlock);

    Event::assertNotDispatched(StartStake::class);

    expect(Stake::whereActive()->get())->toHaveCount(0);
});

it('doesnt pass validation with long duration', function () {

    $duration = config('nom.stake.timeMaxSec') + 1;
    $accountBlock = createStakeAccountBlock([
        'data' => [
            'durationInSec' => $duration,
        ],
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Stake: Stake failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid stake duration')
        )
        ->once();

    StakeAction::run($accountBlock);

    Event::assertNotDispatched(StartStake::class);

    expect(Stake::whereActive()->get())->toHaveCount(0);
});
