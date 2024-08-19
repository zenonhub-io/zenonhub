<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Pillar\UpdatePillar;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Pillar\PillarUpdated;
use App\Domains\Indexer\Factories\MockAccountBlockFactory;
use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Pillar;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'pillar');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createUpdatePillarAccountBlock(array $overrides = []): AccountBlock
{
    $account = $overrides['account'] ?? Account::factory()->create();

    $default = [
        'account' => $account,
        'toAccount' => load_account(EmbeddedContractsEnum::PILLAR->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) 0,
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Pillar', 'UpdatePillar'),
        'data' => '{"name":"Test","producerAddress":"' . $account->address . '","rewardAddress":"' . $account->address . '","giveBlockRewardPercentage":"0","giveDelegateRewardPercentage":"90"}',
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('updates a pillar', function () {

    $owner = Account::factory()->create();
    Pillar::factory()->create([
        'name' => 'Test',
        'owner_id' => $owner->id,
    ]);
    $accountBlock = createUpdatePillarAccountBlock([
        'account' => $owner,
        'data' => json_encode([
            'name' => 'Test',
            'producerAddress' => $owner->address,
            'rewardAddress' => $owner->address,
            'giveBlockRewardPercentage' => '0',
            'giveDelegateRewardPercentage' => '90',
        ]),
    ]);

    (new UpdatePillar)->handle($accountBlock);

    $pillar = Pillar::firstWhere('name', 'Test');

    expect(Pillar::whereActive()->get())->toHaveCount(4)
        ->and($pillar)->not->toBeNull()
        ->and($pillar->withdraw_account_id)->toEqual($owner->id)
        ->and($pillar->producer_account_id)->toEqual($owner->id)
        ->and($pillar->momentum_rewards)->toEqual(0)
        ->and($pillar->delegate_rewards)->toEqual(90);
});

it('dispatches the pillar updated event', function () {

    $pillar = Pillar::factory()->create([
        'name' => 'Test',
        'owner_id' => Account::factory()->create()->id,
    ]);
    $accountBlock = createUpdatePillarAccountBlock([
        'account' => $pillar->owner,
    ]);

    Event::fake();

    (new UpdatePillar)->handle($accountBlock);

    Event::assertDispatched(PillarUpdated::class);
});

it('ensure updates can only come from pillar owner', function () {

    Pillar::factory()->create([
        'name' => 'Test',
    ]);

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Pillar: UpdatePillar failed',
            Mockery::on(fn ($data) => $data['error'] === 'Account is not pillar owner')
        )
        ->once();

    $accountBlock = createUpdatePillarAccountBlock([
        'account' => Account::factory()->create(),
    ]);

    Event::fake();

    (new UpdatePillar)->handle($accountBlock);

    Event::assertNotDispatched(PillarUpdated::class);
});
