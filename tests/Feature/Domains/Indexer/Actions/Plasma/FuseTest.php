<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Plasma\Fuse;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Plasma\StartFuse;
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

uses()->group('indexer', 'indexer-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createFuseAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm'),
        'toAccount' => load_account(EmbeddedContractsEnum::PLASMA->value),
        'token' => load_token(NetworkTokensEnum::QSR->value),
        'amount' => (string) (50 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Plasma', 'Fuse'),
        'data' => '{"address":"z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm"}',
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('create a fuse', function () {

    $accountBlock = createFuseAccountBlock();

    (new Fuse)->handle($accountBlock);

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

    (new Fuse)->handle($accountBlock);

    Event::assertDispatched(StartFuse::class);
});

it('doesnt pass validation with invalid token', function () {

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Plasma: Fuse failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid token, must be QSR')
        )
        ->once();

    $accountBlock = createFuseAccountBlock([
        'token' => load_token(NetworkTokensEnum::ZNN->value),
    ]);

    (new Fuse)->handle($accountBlock);

    expect(Plasma::whereActive()->get())->toHaveCount(0);
});

it('doesnt pass validation with invalid amount of QSR', function () {

    $accountBlock = createFuseAccountBlock([
        'amount' => '50',
    ]);

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Plasma: Fuse failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid amount of QSR')
        )
        ->once();

    (new Fuse)->handle($accountBlock);

    expect(Plasma::whereActive()->get())->toHaveCount(0);
});
