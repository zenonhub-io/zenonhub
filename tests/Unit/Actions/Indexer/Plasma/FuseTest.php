<?php

declare(strict_types=1);

use App\Actions\Indexer\Plasma\Fuse;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Plasma\StartFuse;
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

function createFuseAccountBlock(array $overrides = []): AccountBlock
{
    $account = $overrides['account'] ?? Account::factory()->create();

    $default = [
        'account' => $account,
        'toAccount' => load_account(EmbeddedContractsEnum::PLASMA->value),
        'token' => load_token(NetworkTokensEnum::QSR->zts()),
        'amount' => (string) (50 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Plasma', 'Fuse'),
        'data' => [
            'address' => $account->address,
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('create a fuse', function () {

    $accountBlock = createFuseAccountBlock();

    Fuse::run($accountBlock);

    $plasma = Plasma::first();

    expect(Plasma::whereActive()->get())->toHaveCount(1)
        ->and($plasma->from_account_id)->toEqual($accountBlock->account->id)
        ->and($plasma->to_account_id)->toEqual($accountBlock->account->id)
        ->and($plasma->amount)->toEqual($accountBlock->amount)
        ->and($plasma->started_at)->toEqual($accountBlock->created_at)
        ->and($plasma->ended_at)->toBeNull();
});

it('dispatches the fused event', function () {

    $accountBlock = createFuseAccountBlock();

    Event::fake();

    Fuse::run($accountBlock);

    Event::assertDispatched(StartFuse::class);
});

it('doesnt pass validation with invalid token', function () {

    $accountBlock = createFuseAccountBlock([
        'token' => load_token(NetworkTokensEnum::ZNN->zts()),
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Plasma: Fuse failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid token, must be QSR')
        )
        ->once();

    Fuse::run($accountBlock);

    Event::assertNotDispatched(StartFuse::class);

    expect(Plasma::whereActive()->get())->toHaveCount(0);
});

it('doesnt pass validation with invalid amount of QSR', function () {

    $accountBlock = createFuseAccountBlock([
        'amount' => '50',
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Plasma: Fuse failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid amount of QSR')
        )
        ->once();

    Fuse::run($accountBlock);

    Event::assertNotDispatched(StartFuse::class);

    expect(Plasma::whereActive()->get())->toHaveCount(0);
});
