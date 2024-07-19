<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Plasma\CancelFuse;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Plasma\EndFuse;
use App\Domains\Indexer\Factories\MockAccountBlockFactory;
use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Plasma;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'plasma', 'cancel-fuse');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);

    $account = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    Plasma::create([
        'chain_id' => $account->chain_id,
        'from_account_id' => $account->id,
        'to_account_id' => $account->id,
        'account_block_id' => 1,
        'amount' => 50 * NOM_DECIMALS,
        'hash' => hash('sha256', 'example-hash'),
        'started_at' => now()->subDay(),
        'ended_at' => null,
    ]);
});

function createCancelFuseAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm'),
        'toAccount' => load_account(EmbeddedContractsEnum::PLASMA->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => '0',
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Plasma', 'CancelFuse'),
        'data' => '{"id":"' . hash('sha256', 'example-hash') . '"}',
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('cancels a fuse', function () {

    $accountBlock = createCancelFuseAccountBlock();
    $accountBlock->created_at = now();

    (new CancelFuse)->handle($accountBlock);

    $plasma = Plasma::first();

    expect(Plasma::whereInActive()->get())->toHaveCount(1)
        ->and($plasma->ended_at)->toEqual($accountBlock->created_at);
});

it('dispatches the end fuse event', function () {

    $accountBlock = createCancelFuseAccountBlock();
    $accountBlock->created_at = now();

    Event::fake();

    (new CancelFuse)->handle($accountBlock);

    Event::assertDispatched(EndFuse::class);
});

it('ensures only plasma owners can cancel fuses', function () {

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Plasma: CancelFuse failed',
            Mockery::on(fn ($data) => $data['error'] === 'Account is not plasma owner')
        )
        ->once();

    $accountBlock = createCancelFuseAccountBlock([
        'account' => load_account(config('explorer.empty_address')),
    ]);

    (new CancelFuse)->handle($accountBlock);
});

it('enforces plasma minimum expiration time', function () {

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Plasma: CancelFuse failed',
            Mockery::on(fn ($data) => $data['error'] === 'Plasma not yet cancelable')
        )
        ->once();

    $accountBlock = createCancelFuseAccountBlock();

    (new CancelFuse)->handle($accountBlock);
});
