<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Stake\Stake as StakeAction;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Stake\StartStake;
use App\Domains\Indexer\Factories\MockAccountBlockFactory;
use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Stake;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'stake');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createStakeAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm'),
        'toAccount' => load_account(EmbeddedContractsEnum::STAKE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (100 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Stake', 'Stake'),
        'data' => '{"durationInSec":"31104000"}',
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('create a new stake', function () {

    $accountBlock = createStakeAccountBlock();

    (new StakeAction)->handle($accountBlock);

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

    (new StakeAction)->handle($accountBlock);

    Event::assertDispatched(StartStake::class);
});

it('doesnt pass validation with invalid token', function () {

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Stake: Stake failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid stake token')
        )
        ->once();

    $accountBlock = createStakeAccountBlock([
        'token' => load_token(NetworkTokensEnum::QSR->value),
    ]);

    (new StakeAction)->handle($accountBlock);

    expect(Stake::get())->toHaveCount(0);
});

it('doesnt pass validation with invalid amount of ZNN', function () {

    $accountBlock = createStakeAccountBlock([
        'amount' => '1',
    ]);

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Stake: Stake failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid stake amount')
        )
        ->once();

    (new StakeAction)->handle($accountBlock);

    expect(Stake::whereActive()->get())->toHaveCount(0);
});

it('doesnt pass validation with short duration', function () {

    $duration = config('nom.stake.timeMinSec') - 1;
    $accountBlock = createStakeAccountBlock([
        'data' => '{"durationInSec":"' . $duration . '"}',
    ]);

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Stake: Stake failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid stake duration')
        )
        ->once();

    (new StakeAction)->handle($accountBlock);

    expect(Stake::whereActive()->get())->toHaveCount(0);
});

it('doesnt pass validation with long duration', function () {

    $duration = config('nom.stake.timeMaxSec') + 1;
    $accountBlock = createStakeAccountBlock([
        'data' => '{"durationInSec":"' . $duration . '"}',
    ]);

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Stake: Stake failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid stake duration')
        )
        ->once();

    (new StakeAction)->handle($accountBlock);

    expect(Stake::whereActive()->get())->toHaveCount(0);
});
