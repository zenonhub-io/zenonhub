<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Pillar\Revoke;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Pillar\PillarRevoked;
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

uses()->group('indexer', 'indexer-actions', 'pillar-revoke');

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
        'data' => '{"name":"Test"}',
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

    (new Revoke)->handle($accountBlock);

    $pillar = Pillar::firstWhere('name', 'Test');

    expect(Pillar::whereActive()->get())->toHaveCount(3)
        ->and($pillar)->not->toBeNull()
        ->and($pillar->revoked_at)->toEqual($accountBlock->created_at);
});

it('dispatches the pillar registered event', function () {

    $pillar = Pillar::factory()->create([
        'name' => 'Test',
        'created_at' => now()->subDays(84),
    ]);
    $accountBlock = createRevokePillarAccountBlock([
        'account' => $pillar->owner,
        'createdAt' => now(),
    ]);

    Event::fake();

    (new Revoke)->handle($accountBlock);

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

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Pillar: Revoke failed',
            Mockery::on(fn ($data) => $data['error'] === 'Account is not pillar owner')
        )
        ->once();

    (new Revoke)->handle($accountBlock);

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

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Pillar: Revoke failed',
            Mockery::on(fn ($data) => $data['error'] === 'Pillar not currently revocable')
        )
        ->once();

    (new Revoke)->handle($accountBlock);

    expect(Pillar::whereActive()->get())->toHaveCount(4);
});
