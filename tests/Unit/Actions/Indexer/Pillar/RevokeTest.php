<?php

declare(strict_types=1);

use App\Actions\Indexer\Pillar\Revoke;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Pillar\PillarRevoked;
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

function createRevokePillarAccountBlock(array $overrides = []): AccountBlock
{
    $account = $overrides['account'] ?? Account::factory()->create();

    $default = [
        'account' => $account,
        'toAccount' => load_account(EmbeddedContractsEnum::PILLAR->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) 0,
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Pillar', 'Revoke'),
        'data' => [
            'name' => 'Test',
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('revokes a pillar', function () {

    $pillar = Pillar::factory()->create([
        'name' => 'Test',
        'created_at' => now()->subDays(84),
    ]);
    $accountBlock = createRevokePillarAccountBlock([
        'account' => $pillar->owner,
        'createdAt' => now(),
    ]);

    Revoke::run($accountBlock);

    $pillar = Pillar::firstWhere('name', 'Test');

    expect(Pillar::whereActive()->get())->toHaveCount(3)
        ->and($pillar)->not->toBeNull()
        ->and($pillar->revoked_at)->toEqual($accountBlock->created_at);
});

it('dispatches the pillar revoked event', function () {

    $pillar = Pillar::factory()->create([
        'name' => 'Test',
        'created_at' => now()->subDays(84),
    ]);
    $accountBlock = createRevokePillarAccountBlock([
        'account' => $pillar->owner,
        'createdAt' => now(),
    ]);

    Event::fake();

    Revoke::run($accountBlock);

    Event::assertDispatched(PillarRevoked::class);
});

it('ensure pillars can only be revoked by owner', function () {

    Pillar::factory()->create([
        'name' => 'Test',
        'created_at' => now()->subDays(84),
    ]);
    $accountBlock = createRevokePillarAccountBlock([
        'createdAt' => now(),
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Pillar: Revoke failed',
            Mockery::on(fn ($data) => $data['error'] === 'Account is not pillar owner')
        )
        ->once();

    Revoke::run($accountBlock);

    Event::assertNotDispatched(PillarRevoked::class);

    expect(Pillar::whereActive()->get())->toHaveCount(4);
});

it('enforce the pillars revocable time window', function () {

    $pillar = Pillar::factory()->create([
        'name' => 'Test',
        'created_at' => now()->subDays(80),
    ]);
    $accountBlock = createRevokePillarAccountBlock([
        'account' => $pillar->owner,
        'createdAt' => now(),
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Pillar: Revoke failed',
            Mockery::on(fn ($data) => $data['error'] === 'Pillar not currently revocable')
        )
        ->once();

    Revoke::run($accountBlock);

    Event::assertNotDispatched(PillarRevoked::class);

    expect(Pillar::whereActive()->get())->toHaveCount(4);
});
