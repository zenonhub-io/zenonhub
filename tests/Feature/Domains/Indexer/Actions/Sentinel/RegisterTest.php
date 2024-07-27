<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Sentinel\Register;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Sentinel\SentinelRegistered;
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
});

function createSentinelRegisterAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm'),
        'toAccount' => load_account(EmbeddedContractsEnum::SENTINEL->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (5000 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Sentinel', 'Register'),
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('registers a new sentinel', function () {

    $accountBlock = createSentinelRegisterAccountBlock();

    (new Register)->handle($accountBlock);

    $sentinel = Sentinel::first();

    expect(Sentinel::whereActive()->get())->toHaveCount(1)
        ->and($sentinel->created_at)->toEqual($accountBlock->created_at);
});

it('dispatches the sentinel registered event', function () {

    $accountBlock = createSentinelRegisterAccountBlock();

    Event::fake();

    (new Register)->handle($accountBlock);

    Event::assertDispatched(SentinelRegistered::class);
});

it('ensure sentinels can only be registered with ZNN tokens', function () {

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Sentinel: Register failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid token')
        )
        ->once();

    $accountBlock = createSentinelRegisterAccountBlock([
        'token' => load_token(NetworkTokensEnum::QSR->value),
    ]);

    (new Register)->handle($accountBlock);

    expect(Sentinel::whereActive()->get())->toHaveCount(0);
});

it('enforces the required registration cost', function () {

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Sentinel: Register failed',
            Mockery::on(fn ($data) => $data['error'] === 'Amount doesnt match sentinel registration cost')
        )
        ->once();

    $accountBlock = createSentinelRegisterAccountBlock([
        'amount' => config('nom.sentinel.znnRegisterAmount') + 1,
    ]);

    (new Register)->handle($accountBlock);

    expect(Sentinel::whereActive()->get())->toHaveCount(0);
});
