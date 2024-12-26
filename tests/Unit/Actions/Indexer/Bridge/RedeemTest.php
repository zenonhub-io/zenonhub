<?php

declare(strict_types=1);

use App\Actions\Indexer\Bridge\Redeem;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\UnwrapRedeemed;
use App\Factories\MockAccountBlockFactory;
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

function createRedeemRequestAccountBlock(array $overrides = []): AccountBlock
{
    $unwrap = BridgeUnwrap::factory()->create();

    $default = [
        'account' => load_account(config('nom.bridge.initialBridgeAdmin')),
        'toAccount' => load_account(EmbeddedContractsEnum::BRIDGE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Bridge', 'Redeem'),
        'data' => [
            'transactionHash' => $unwrap->transaction_hash,
            'logIndex' => $unwrap->log_index,
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('redeems a unwrap request', function () {

    $accountBlock = createRedeemRequestAccountBlock();

    Redeem::run($accountBlock);

    $unwrap = BridgeUnwrap::first();

    expect(BridgeUnwrap::get())->toHaveCount(1)
        ->and($unwrap->redeemed_at)->not->toBeNull();
});

it('dispatches unwrap redeemed event', function () {

    $accountBlock = createRedeemRequestAccountBlock();

    Event::fake();

    Redeem::run($accountBlock);

    Event::assertDispatched(UnwrapRedeemed::class);
});

it('ensures only valid unwraps can be redeemed', function () {

    $accountBlock = createRedeemRequestAccountBlock([
        'data' => [
            'transactionHash' => '123',
            'logIndex' => '123',
        ],
    ]);

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Bridge: Redeem failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid unwrap')
        )
        ->once();

    Redeem::run($accountBlock);

    Event::assertNotDispatched(UnwrapRedeemed::class);

    expect(BridgeUnwrap::first()->redeemed_at)->toBeNull();
});
