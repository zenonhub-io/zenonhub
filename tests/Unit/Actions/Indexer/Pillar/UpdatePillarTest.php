<?php

declare(strict_types=1);

use App\Actions\Indexer\Pillar\UpdatePillar;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Pillar\PillarUpdated;
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
        'data' => [
            'name' => 'Test',
            'producerAddress' => $account->address,
            'rewardAddress' => $account->address,
            'giveBlockRewardPercentage' => '0',
            'giveDelegateRewardPercentage' => '90',
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('updates a pillar', function () {

    $owner = Account::factory()->create();
    $producer = Account::factory()->create();
    $withdrawal = Account::factory()->create();
    Pillar::factory()->create([
        'name' => 'Test',
        'owner_id' => $owner->id,
    ]);
    $accountBlock = createUpdatePillarAccountBlock([
        'account' => $owner,
        'data' => [
            'name' => 'Test',
            'producerAddress' => $producer->address,
            'rewardAddress' => $withdrawal->address,
            'giveBlockRewardPercentage' => '69',
            'giveDelegateRewardPercentage' => '69',
        ],
    ]);

    (new UpdatePillar)->handle($accountBlock);

    $pillar = Pillar::firstWhere('name', 'Test');

    expect($pillar)->not->toBeNull()
        ->and($pillar->withdraw_account_id)->toEqual($withdrawal->id)
        ->and($pillar->producer_account_id)->toEqual($producer->id)
        ->and($pillar->momentum_rewards)->toEqual(69)
        ->and($pillar->delegate_rewards)->toEqual(69);
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
    $accountBlock = createUpdatePillarAccountBlock([
        'account' => Account::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Pillar: UpdatePillar failed',
            Mockery::on(fn ($data) => $data['error'] === 'Account is not pillar owner')
        )
        ->once();

    (new UpdatePillar)->handle($accountBlock);

    Event::assertNotDispatched(PillarUpdated::class);
});
