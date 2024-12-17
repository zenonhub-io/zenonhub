<?php

declare(strict_types=1);

use App\Actions\Indexer\Bridge\RemoveTokenPair;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Bridge\TokenPairRemoved;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\BridgeNetworkToken;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Token;
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

function createRemoveTokenPaidAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => load_account(config('nom.bridge.initialBridgeAdmin')),
        'toAccount' => load_account(EmbeddedContractsEnum::BRIDGE->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) (1 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Bridge', 'RemoveTokenPair'),
        'data' => [
            'networkClass' => '321',
            'chainId' => '1',
            'tokenStandard' => Token::factory()->create()->token_standard,
            'tokenAddress' => '0x' . bin2hex(random_bytes(20)),
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('removes an existing network token', function () {

    $token = Token::factory()->create();
    $tokenAddress = '0x' . bin2hex(random_bytes(20));
    BridgeNetworkToken::factory()->create([
        'bridge_network_id' => BridgeNetwork::factory()->create([
            'network_class' => '321',
        ])->id,
        'token_id' => $token->id,
        'token_address' => $tokenAddress,
    ]);

    $accountBlock = createRemoveTokenPaidAccountBlock([
        'data' => [
            'networkClass' => '321',
            'chainId' => '1',
            'tokenStandard' => $token->token_standard,
            'tokenAddress' => $tokenAddress,
        ],
    ]);

    RemoveTokenPair::run($accountBlock);

    expect(BridgeNetworkToken::get())->toHaveCount(0);
});

it('dispatches the network set event', function () {

    $token = Token::factory()->create();
    $tokenAddress = '0x' . bin2hex(random_bytes(20));
    BridgeNetworkToken::factory()->create([
        'bridge_network_id' => BridgeNetwork::factory()->create([
            'network_class' => '321',
        ])->id,
        'token_id' => $token->id,
        'token_address' => $tokenAddress,
    ]);

    $accountBlock = createRemoveTokenPaidAccountBlock([
        'data' => [
            'networkClass' => '321',
            'chainId' => '1',
            'tokenStandard' => $token->token_standard,
            'tokenAddress' => $tokenAddress,
        ],
    ]);

    Event::fake();

    RemoveTokenPair::run($accountBlock);

    Event::assertDispatched(TokenPairRemoved::class);
});

it('ensures only the bridge admin can set a new network', function () {

    $token = Token::factory()->create();
    $tokenAddress = '0x' . bin2hex(random_bytes(20));
    BridgeNetworkToken::factory()->create([
        'bridge_network_id' => BridgeNetwork::factory()->create([
            'network_class' => '321',
        ])->id,
        'token_id' => $token->id,
        'token_address' => $tokenAddress,
    ]);

    $accountBlock = createRemoveTokenPaidAccountBlock([
        'account' => Account::factory()->create(),
        'data' => [
            'networkClass' => '321',
            'chainId' => '1',
            'tokenStandard' => $token->token_standard,
            'tokenAddress' => $tokenAddress,
        ],
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Bridge: RemoveTokenPair failed',
            Mockery::on(fn ($data) => $data['error'] === 'Action sent from non admin')
        )
        ->once();

    RemoveTokenPair::run($accountBlock);

    Event::assertNotDispatched(TokenPairRemoved::class);

    expect(BridgeNetworkToken::get())->toHaveCount(1);
});
