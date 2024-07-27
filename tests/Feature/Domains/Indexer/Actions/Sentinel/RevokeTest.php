<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Sentinel\Revoke;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Sentinel\SentinelRevoked;
use App\Domains\Indexer\Factories\MockAccountBlockFactory;
use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Sentinel;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'sentinel');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);

    Sentinel::create([
        'chain_id' => 1,
        'owner_id' => load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm')->id,
        'created_at' => now()->subDays(28), // IMPORTANT - This is in the revocable window
    ]);
});

function createSentinelRevokeAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm'),
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

    $accountBlock = createSentinelRevokeAccountBlock();
    $accountBlock->created_at = now();

    (new Revoke)->handle($accountBlock);

    $sentinel = Sentinel::first();

    expect(Sentinel::whereInactive()->get())->toHaveCount(1)
        ->and($sentinel->revoked_at)->toEqual($accountBlock->created_at);
});

it('dispatches the sentinel registered event', function () {

    $accountBlock = createSentinelRevokeAccountBlock();
    $accountBlock->created_at = now();

    Event::fake();

    (new Revoke)->handle($accountBlock);

    Event::assertDispatched(SentinelRevoked::class);
});

it('ensure sentinels can only be revoked once', function () {

    Sentinel::query()
        ->update([
            'revoked_at' => now(),
        ]);

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Sentinel: Revoke failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid sentinel')
        )
        ->once();

    $accountBlock = createSentinelRevokeAccountBlock();
    $accountBlock->created_at = now();

    (new Revoke)->handle($accountBlock);

    expect(Sentinel::whereInactive()->get())->toHaveCount(1);
});

it('enforce the sentinel revocable time window', function () {

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Sentinel: Revoke failed',
            Mockery::on(fn ($data) => $data['error'] === 'Sentinel not revocable')
        )
        ->once();

    $accountBlock = createSentinelRevokeAccountBlock();
    $accountBlock->created_at = now()->subDays(31);

    (new Revoke)->handle($accountBlock);

    expect(Sentinel::whereInactive()->get())->toHaveCount(0);
});
