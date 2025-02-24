<?php

declare(strict_types=1);

use App\Actions\Indexer\Pillar\Register;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Pillar\PillarRegistered;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Pillar;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Nom\NetworkSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'pillar-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NetworkSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createPillarRegisterAccountBlock(array $overrides = []): AccountBlock
{
    $account = $overrides['account'] ?? Account::factory()->create();

    $default = [
        'account' => $account,
        'toAccount' => load_account(EmbeddedContractsEnum::PILLAR->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (15000 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Pillar', 'Register'),
        'data' => [
            'name' => 'Test',
            'producerAddress' => $account->address,
            'rewardAddress' => $account->address,
            'giveBlockRewardPercentage' => '100',
            'giveDelegateRewardPercentage' => '100',
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('registers a new pillar', function () {

    $owner = Account::factory()->create();
    $accountBlock = createPillarRegisterAccountBlock([
        'account' => $owner,
    ]);

    Register::run($accountBlock);

    $pillar = Pillar::firstWhere('name', 'Test');

    expect(Pillar::whereActive()->get())->toHaveCount(4)
        ->and($pillar)->not->toBeNull()
        ->and($pillar->owner_id)->toEqual($owner->id)
        ->and($pillar->withdraw_account_id)->toEqual($owner->id)
        ->and($pillar->producer_account_id)->toEqual($owner->id)
        ->and($pillar->momentum_rewards)->toEqual(100)
        ->and($pillar->delegate_rewards)->toEqual(100)
        ->and($pillar->producer_account_id)->toEqual($owner->id)
        ->and($pillar->qsr_burn)->toEqual(160000 * NOM_DECIMALS)
        ->and($pillar->is_legacy)->toBeFalse()
        ->and($pillar->created_at)->toEqual($accountBlock->created_at);
});

it('dispatches the pillar registered event', function () {

    $accountBlock = createPillarRegisterAccountBlock();

    Event::fake();

    Register::run($accountBlock);

    Event::assertDispatched(PillarRegistered::class);
});

it('ensure pillars can only be registered with ZNN tokens', function () {

    $accountBlock = createPillarRegisterAccountBlock([
        'token' => load_token(NetworkTokensEnum::QSR->value),
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Pillar: Register failed',
            Mockery::on(fn ($data) => $data['error'] === 'Token must be ZNN')
        )
        ->once();

    Register::run($accountBlock);

    Event::assertNotDispatched(PillarRegistered::class);

    expect(Pillar::whereActive()->get())->toHaveCount(3);
});

it('enforces the required registration cost', function () {

    $accountBlock = createPillarRegisterAccountBlock([
        'amount' => config('nom.pillar.znnStakeAmount') + 1,
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Pillar: Register failed',
            Mockery::on(fn ($data) => $data['error'] === 'Amount doesnt match pillar registration cost')
        )
        ->once();

    Register::run($accountBlock);

    Event::assertNotDispatched(PillarRegistered::class);

    expect(Pillar::whereActive()->get())->toHaveCount(3);
});
