<?php

declare(strict_types=1);

use App\Actions\Indexer\Bridge\UpdateWrapRequest;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\WrapRequestUpdated;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeWrap;
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

function createUpdateWrapRequestAccountBlock(array $overrides = []): AccountBlock
{
    $wrap = BridgeWrap::factory()->create();

    $default = [
        'account' => $wrap->account,
        'toAccount' => load_account(EmbeddedContractsEnum::BRIDGE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Bridge', 'UpdateWrapRequest'),
        'data' => [
            'id' => $wrap->accountBlock->hash,
            'signature' => bin2hex(random_bytes(10)),
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('updates a token wrap request', function () {

    $accountBlock = createUpdateWrapRequestAccountBlock();

    (new UpdateWrapRequest)->handle($accountBlock);

    $wrap = BridgeWrap::first();

    expect(BridgeWrap::get())->toHaveCount(1)
        ->and($wrap->signature)->toEqual($accountBlock->data->decoded['signature']);
});

it('dispatches the wrap request updated event', function () {

    $accountBlock = createUpdateWrapRequestAccountBlock();

    Event::fake();

    (new UpdateWrapRequest)->handle($accountBlock);

    Event::assertDispatched(WrapRequestUpdated::class);
});

it('ensures wraps only happen on valid bridge networks', function () {

    $accountBlock = createUpdateWrapRequestAccountBlock([
        'data' => [
            'id' => AccountBlock::factory()->create()->hash,
            'signature' => bin2hex(random_bytes(10)),
        ],
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: UpdateWrapRequest failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid wrap')
        )
        ->once();

    (new UpdateWrapRequest)->handle($accountBlock);

    Event::assertNotDispatched(WrapRequestUpdated::class);

    expect(BridgeWrap::get())->toHaveCount(1)
        ->and(BridgeWrap::first()->signature)->toBeNull();
});
