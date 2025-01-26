<?php

declare(strict_types=1);

use App\Actions\Indexer\Pillar\RegisterLegacy;
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
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'pillar-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createPillarRegisterLegacyAccountBlock(array $overrides = []): AccountBlock
{
    $account = $overrides['account'] ?? Account::factory()->create();

    $default = [
        'account' => $account,
        'toAccount' => load_account(EmbeddedContractsEnum::PILLAR->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (15000 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Pillar', 'RegisterLegacy'),
        'data' => [
            'name' => 'Test',
            'producerAddress' => $account->address,
            'rewardAddress' => $account->address,
            'giveBlockRewardPercentage' => '100',
            'giveDelegateRewardPercentage' => '100',
            'publicKey' => 'BPDpXeyogBOyTOp41ozqDPOnJV+d5ucgLyikCYrdfoOcfE1rvMcr+FRALQMQmDjPlMSPh8C4i7jOvFHdYSx757c=',
            'signature' => 'H\/GcB2n1hHPutTOCa6lRRJYExajY8uVrr9ockrjVN0xTP0d63wKbwQyC2dVuk22ol01Hp1BIlV\/445Oc5xKzG54=',
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('registers a new pillar', function () {

    $owner = Account::factory()->create();
    $accountBlock = createPillarRegisterLegacyAccountBlock([
        'account' => $owner,
    ]);

    RegisterLegacy::run($accountBlock);

    $pillar = Pillar::firstWhere('name', 'Test');

    expect(Pillar::whereActive()->get())->toHaveCount(4)
        ->and($pillar)->not->toBeNull()
        ->and($pillar->owner_id)->toEqual($owner->id)
        ->and($pillar->withdraw_account_id)->toEqual($owner->id)
        ->and($pillar->producer_account_id)->toEqual($owner->id)
        ->and($pillar->momentum_rewards)->toEqual(100)
        ->and($pillar->delegate_rewards)->toEqual(100)
        ->and($pillar->producer_account_id)->toEqual($owner->id)
        ->and($pillar->qsr_burn)->toEqual(150000 * NOM_DECIMALS)
        ->and($pillar->is_legacy)->toBeTrue()
        ->and($pillar->created_at)->toEqual($accountBlock->created_at);
});

it('dispatches the pillar registered event', function () {

    $accountBlock = createPillarRegisterLegacyAccountBlock();

    Event::fake();

    RegisterLegacy::run($accountBlock);

    Event::assertDispatched(PillarRegistered::class);
});

it('ensure pillars can only be registered with ZNN tokens', function () {

    $accountBlock = createPillarRegisterLegacyAccountBlock([
        'token' => load_token(NetworkTokensEnum::QSR->value),
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Pillar: RegisterLegacy failed',
            Mockery::on(fn ($data) => $data['error'] === 'Token must be ZNN')
        )
        ->once();

    RegisterLegacy::run($accountBlock);

    Event::assertNotDispatched(PillarRegistered::class);

    expect(Pillar::whereActive()->get())->toHaveCount(3);
});

it('enforces the required registration cost', function () {

    $accountBlock = createPillarRegisterLegacyAccountBlock([
        'amount' => config('nom.pillar.znnStakeAmount') + 1,
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Pillar: RegisterLegacy failed',
            Mockery::on(fn ($data) => $data['error'] === 'Amount doesnt match pillar registration cost')
        )
        ->once();

    RegisterLegacy::run($accountBlock);

    Event::assertNotDispatched(PillarRegistered::class);

    expect(Pillar::whereActive()->get())->toHaveCount(3);
});
