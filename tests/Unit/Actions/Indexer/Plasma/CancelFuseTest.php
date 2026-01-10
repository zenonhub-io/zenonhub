<?php

declare(strict_types=1);

use App\Actions\Indexer\Plasma\CancelFuse;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Plasma\EndFuse;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Plasma;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Nom\NetworkSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'plasma-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NetworkSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createCancelFuseAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::PLASMA->value),
        'token' => load_token(NetworkTokensEnum::ZNN->zts()),
        'amount' => '0',
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Plasma', 'CancelFuse'),
        'data' => [
            'id' => hash('sha256', 'example-hash'),
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('cancels a fuse', function () {

    $plasma = Plasma::factory()->create(attributes: [
        'started_at' => now()->subDay(),
    ]);
    $accountBlock = createCancelFuseAccountBlock([
        'account' => $plasma->fromAccount,
        'data' => [
            'id' => $plasma->accountBlock->hash,
        ],
    ]);

    CancelFuse::run($accountBlock);

    $plasma = Plasma::first();

    expect(Plasma::whereInactive()->get())->toHaveCount(1)
        ->and($plasma->ended_at)->toEqual($accountBlock->created_at);
});

it('dispatches the end fuse event', function () {

    $plasma = Plasma::factory()->create([
        'started_at' => now()->subDay(),
    ]);
    $accountBlock = createCancelFuseAccountBlock([
        'account' => $plasma->fromAccount,
        'data' => [
            'id' => $plasma->accountBlock->hash,
        ],
    ]);

    Event::fake();

    CancelFuse::run($accountBlock);

    Event::assertDispatched(EndFuse::class);
});

it('ensures only plasma owners can cancel fuses', function () {

    $plasma = Plasma::factory()->create([
        'started_at' => now()->subDay(),
    ]);
    $accountBlock = createCancelFuseAccountBlock([
        'data' => [
            'id' => $plasma->accountBlock->hash,
        ],
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Plasma: CancelFuse failed',
            Mockery::on(fn ($data) => $data['error'] === 'Account is not plasma owner')
        )
        ->once();

    CancelFuse::run($accountBlock);

    Event::assertNotDispatched(EndFuse::class);

    expect(Plasma::whereActive()->get())->toHaveCount(1);
});

it('enforces plasma minimum expiration time', function () {

    $plasma = Plasma::factory()->create([
        'started_at' => now()->subHour(),
    ]);
    $accountBlock = createCancelFuseAccountBlock([
        'account' => $plasma->fromAccount,
        'data' => [
            'id' => $plasma->accountBlock->hash,
        ],
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Plasma: CancelFuse failed',
            Mockery::on(fn ($data) => $data['error'] === 'Plasma not yet cancelable')
        )
        ->once();

    CancelFuse::run($accountBlock);

    Event::assertNotDispatched(EndFuse::class);

    expect(Plasma::whereActive()->get())->toHaveCount(1);
});
