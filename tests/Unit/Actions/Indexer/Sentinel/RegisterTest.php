<?php

declare(strict_types=1);

use App\Actions\Indexer\Sentinel\Register;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Sentinel\SentinelRegistered;
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

function createSentinelRegisterAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
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

    Register::run($accountBlock);

    $sentinel = Sentinel::first();

    expect(Sentinel::whereActive()->get())->toHaveCount(1)
        ->and($sentinel->created_at)->toEqual($accountBlock->created_at);
});

it('dispatches the sentinel registered event', function () {

    $accountBlock = createSentinelRegisterAccountBlock();

    Event::fake();

    Register::run($accountBlock);

    Event::assertDispatched(SentinelRegistered::class);
});

it('ensure sentinels can only be registered with ZNN tokens', function () {

    $accountBlock = createSentinelRegisterAccountBlock([
        'token' => load_token(NetworkTokensEnum::QSR->value),
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Sentinel: Register failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid token')
        )
        ->once();

    Register::run($accountBlock);

    Event::assertNotDispatched(SentinelRegistered::class);

    expect(Sentinel::whereActive()->get())->toHaveCount(0);
});

it('enforces the required registration cost', function () {

    $accountBlock = createSentinelRegisterAccountBlock([
        'amount' => config('nom.sentinel.znnRegisterAmount') + 1,
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Sentinel: Register failed',
            Mockery::on(fn ($data) => $data['error'] === 'Amount doesnt match sentinel registration cost')
        )
        ->once();

    Register::run($accountBlock);

    Event::assertNotDispatched(SentinelRegistered::class);

    expect(Sentinel::whereActive()->get())->toHaveCount(0);
});
