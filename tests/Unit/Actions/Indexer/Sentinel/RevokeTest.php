<?php

declare(strict_types=1);

use App\Actions\Indexer\Sentinel\Revoke;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Sentinel\SentinelRevoked;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Sentinel;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'sentinel-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createSentinelRevokeAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::SENTINEL->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Sentinel', 'Revoke'),
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('revokes an existing sentinel', function () {

    $accountBlock = createSentinelRevokeAccountBlock([
        'account' => Sentinel::factory()->create([
            'created_at' => now()->subDays(28),
        ])->owner,
    ]);
    $accountBlock->created_at = now();

    (new Revoke)->handle($accountBlock);

    $sentinel = Sentinel::first();

    expect(Sentinel::whereInactive()->get())->toHaveCount(1)
        ->and($sentinel->revoked_at)->toEqual($accountBlock->created_at);
});

it('dispatches the sentinel registered event', function () {

    $accountBlock = createSentinelRevokeAccountBlock([
        'account' => Sentinel::factory()->create([
            'created_at' => now()->subDays(28),
        ])->owner,
    ]);
    $accountBlock->created_at = now();

    Event::fake();

    (new Revoke)->handle($accountBlock);

    Event::assertDispatched(SentinelRevoked::class);
});

it('ensure sentinels can only be revoked once', function () {

    Sentinel::factory()->revoked()->create([
        'created_at' => now()->subDays(28),
    ]);

    $accountBlock = createSentinelRevokeAccountBlock();
    $accountBlock->created_at = now();

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Sentinel: Revoke failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid sentinel')
        )
        ->once();

    (new Revoke)->handle($accountBlock);

    Event::assertNotDispatched(SentinelRevoked::class);

    expect(Sentinel::whereInactive()->get())->toHaveCount(1);
});

it('enforce the sentinel revocable time window', function () {

    $accountBlock = createSentinelRevokeAccountBlock([
        'account' => Sentinel::factory()->create([
            'created_at' => now()->subDays(28),
        ])->owner,
    ]);
    $accountBlock->created_at = now()->subDays(31);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Sentinel: Revoke failed',
            Mockery::on(fn ($data) => $data['error'] === 'Sentinel not revocable')
        )
        ->once();

    (new Revoke)->handle($accountBlock);

    Event::assertNotDispatched(SentinelRevoked::class);

    expect(Sentinel::whereInactive()->get())->toHaveCount(0);
});
