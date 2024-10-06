<?php

declare(strict_types=1);

use App\Actions\Indexer\Bridge\RevokeUnwrapRequest;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\UnwrapRequestRevoked;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeUnwrap;
use App\Models\Nom\ContractMethod;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'bridge-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createRevokeUnwrapRequestAccountBlock(array $overrides = []): AccountBlock
{
    $unwrap = BridgeUnwrap::factory()->create();

    $default = [
        'account' => load_account(config('nom.bridge.initialBridgeAdmin')),
        'toAccount' => load_account(EmbeddedContractsEnum::BRIDGE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Bridge', 'RevokeUnwrapRequest'),
        'data' => [
            'transactionHash' => $unwrap->transaction_hash,
            'logIndex' => $unwrap->log_index,
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('revokes a unwrap request', function () {

    $accountBlock = createRevokeUnwrapRequestAccountBlock();

    (new RevokeUnwrapRequest)->handle($accountBlock);

    $unwrap = BridgeUnwrap::first();

    expect(BridgeUnwrap::get())->toHaveCount(1)
        ->and($unwrap->revoked_at)->not->toBeNull();
});

it('dispatches the unwrap request revoked event', function () {

    $accountBlock = createRevokeUnwrapRequestAccountBlock();

    Event::fake();

    (new RevokeUnwrapRequest)->handle($accountBlock);

    Event::assertDispatched(UnwrapRequestRevoked::class);
});

it('ensures only valid unwraps can be revoked', function () {

    $accountBlock = createRevokeUnwrapRequestAccountBlock([
        'data' => [
            'transactionHash' => '123',
            'logIndex' => '123',
        ],
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: RevokeUnwrapRequest failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid unwrap')
        )
        ->once();

    (new RevokeUnwrapRequest)->handle($accountBlock);

    Event::assertNotDispatched(UnwrapRequestRevoked::class);

    expect(BridgeUnwrap::first()->revoked_at)->toBeNull();
});

it('ensures only bridge admin can revoke unwrap request', function () {

    $accountBlock = createRevokeUnwrapRequestAccountBlock([
        'account' => Account::factory()->create(),
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: RevokeUnwrapRequest failed',
            Mockery::on(fn ($data) => $data['error'] === 'Action sent from non admin')
        )
        ->once();

    (new RevokeUnwrapRequest)->handle($accountBlock);

    Event::assertNotDispatched(UnwrapRequestRevoked::class);

    expect(BridgeUnwrap::first()->revoked_at)->toBeNull();
});
