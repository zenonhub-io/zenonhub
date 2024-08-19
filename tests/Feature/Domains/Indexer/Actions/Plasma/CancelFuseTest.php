<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Plasma\CancelFuse;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Plasma\EndFuse;
use App\Domains\Indexer\Factories\MockAccountBlockFactory;
use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Plasma;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'plasma');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createCancelFuseAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
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

    $plasma = Plasma::factory()->create(attributes: [
        'started_at' => now()->subDay(),
    ]);
    $accountBlock = createCancelFuseAccountBlock([
        'account' => $plasma->fromAccount,
        'data' => json_encode([
            'id' => $plasma->hash,
        ]),
    ]);

    (new CancelFuse)->handle($accountBlock);

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
        'data' => json_encode([
            'id' => $plasma->hash,
        ]),
    ]);

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

    $plasma = Plasma::factory()->create([
        'started_at' => now()->subDay(),
    ]);
    $accountBlock = createCancelFuseAccountBlock([
        'data' => json_encode([
            'id' => $plasma->hash,
        ]),
    ]);

    (new CancelFuse)->handle($accountBlock);

    expect(Plasma::whereActive()->get())->toHaveCount(1);
});

it('enforces plasma minimum expiration time', function () {

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Plasma: CancelFuse failed',
            Mockery::on(fn ($data) => $data['error'] === 'Plasma not yet cancelable')
        )
        ->once();

    $plasma = Plasma::factory()->create([
        'started_at' => now()->subHour(),
    ]);
    $accountBlock = createCancelFuseAccountBlock([
        'account' => $plasma->fromAccount,
        'data' => json_encode([
            'id' => $plasma->hash,
        ]),
    ]);

    (new CancelFuse)->handle($accountBlock);

    expect(Plasma::whereActive()->get())->toHaveCount(1);
});
