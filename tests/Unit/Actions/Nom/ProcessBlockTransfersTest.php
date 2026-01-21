<?php

declare(strict_types=1);

use App\Actions\Nom\ProcessBlockTransfers;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Nom\NetworkSeeder;
use Database\Seeders\TestGenesisSeeder;

uses()->group('nom', 'nom-actions', 'process-block-transfers');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NetworkSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

it('updates sender account balance when processing a send block', function () {

    $token = load_token(NetworkTokensEnum::ZNN->zts());
    $sender = Account::factory()->create([
        'znn_balance' => (string) (10 * NOM_DECIMALS),
        'znn_sent' => '0',
    ]);
    $receiver = Account::factory()->create();

    $sender->tokens()->attach($token->id, [
        'balance' => (string) (10 * NOM_DECIMALS),
    ]);

    $block = AccountBlock::factory()->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'block_type' => AccountBlockTypesEnum::SEND->value,
        'amount' => (string) (3 * NOM_DECIMALS),
    ]);

    ProcessBlockTransfers::run($block);

    $sender->refresh();

    expect($sender->znn_balance)->toBe((string) (7 * NOM_DECIMALS))
        ->and($sender->znn_sent)->toBe((string) (3 * NOM_DECIMALS));

    $senderBalance = $sender->tokens()
        ->where('token_id', $token->id)
        ->first();

    expect($senderBalance->pivot->balance)->toBe((string) (7 * NOM_DECIMALS));
});

it('updates receiver account balance when processing a receive block', function () {

    $token = load_token(NetworkTokensEnum::ZNN->zts());
    $sender = Account::factory()->create();
    $receiver = Account::factory()->create([
        'znn_balance' => '0',
        'znn_received' => '0',
    ]);

    $sendBlock = AccountBlock::factory()->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'block_type' => AccountBlockTypesEnum::SEND->value,
        'amount' => (string) (5 * NOM_DECIMALS),
    ]);

    $receiveBlock = AccountBlock::factory()->create([
        'account_id' => $receiver->id,
        'block_type' => AccountBlockTypesEnum::RECEIVE->value,
        'paired_account_block_id' => $sendBlock->id,
        'amount' => '0',
    ]);

    ProcessBlockTransfers::run($receiveBlock);

    $receiver->refresh();

    expect($receiver->znn_balance)->toBe((string) (5 * NOM_DECIMALS))
        ->and($receiver->znn_received)->toBe((string) (5 * NOM_DECIMALS));

    $receiverBalance = $receiver->tokens()
        ->where('token_id', $token->id)
        ->first();

    expect($receiverBalance->pivot->balance)->toBe((string) (5 * NOM_DECIMALS));
});

it('updates znn balances correctly for multiple transactions', function () {

    $token = load_token(NetworkTokensEnum::ZNN->zts());
    $sender = Account::factory()->create([
        'znn_balance' => (string) (10 * NOM_DECIMALS),
        'znn_sent' => '0',
    ]);
    $receiver = Account::factory()->create([
        'znn_balance' => (string) (5 * NOM_DECIMALS),
        'znn_received' => '0',
    ]);

    $sender->tokens()->attach($token->id, [
        'balance' => (string) (10 * NOM_DECIMALS),
    ]);

    $receiver->tokens()->attach($token->id, [
        'balance' => (string) (5 * NOM_DECIMALS),
    ]);

    // Sender sends 3 ZNN
    $sendBlock1 = AccountBlock::factory()->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'block_type' => AccountBlockTypesEnum::SEND->value,
        'amount' => (string) (3 * NOM_DECIMALS),
    ]);

    ProcessBlockTransfers::run($sendBlock1);

    // Receiver receives 3 ZNN
    $receiveBlock1 = AccountBlock::factory()->create([
        'account_id' => $receiver->id,
        'block_type' => AccountBlockTypesEnum::RECEIVE->value,
        'paired_account_block_id' => $sendBlock1->id,
        'amount' => '0',
    ]);

    ProcessBlockTransfers::run($receiveBlock1);

    // Receiver sends back 2 ZNN
    $sendBlock2 = AccountBlock::factory()->create([
        'account_id' => $receiver->id,
        'to_account_id' => $sender->id,
        'token_id' => $token->id,
        'block_type' => AccountBlockTypesEnum::SEND->value,
        'amount' => (string) (2 * NOM_DECIMALS),
    ]);

    ProcessBlockTransfers::run($sendBlock2);

    $sender->refresh();
    $receiver->refresh();

    expect($sender->znn_balance)->toBe((string) (7 * NOM_DECIMALS))
        ->and($sender->znn_sent)->toBe((string) (3 * NOM_DECIMALS))
        ->and($receiver->znn_balance)->toBe((string) (6 * NOM_DECIMALS))
        ->and($receiver->znn_received)->toBe((string) (3 * NOM_DECIMALS))
        ->and($receiver->znn_sent)->toBe((string) (2 * NOM_DECIMALS));
});

it('updates qsr balances correctly', function () {

    $token = load_token(NetworkTokensEnum::QSR->zts());
    $sender = Account::factory()->create([
        'qsr_balance' => (string) (10 * NOM_DECIMALS),
        'qsr_sent' => '0',
    ]);
    $receiver = Account::factory()->create([
        'qsr_balance' => '0',
        'qsr_received' => '0',
    ]);

    $sender->tokens()->attach($token->id, [
        'balance' => (string) (10 * NOM_DECIMALS),
    ]);

    $sendBlock = AccountBlock::factory()->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'block_type' => AccountBlockTypesEnum::SEND->value,
        'amount' => (string) (4 * NOM_DECIMALS),
    ]);

    ProcessBlockTransfers::run($sendBlock);

    $receiveBlock = AccountBlock::factory()->create([
        'account_id' => $receiver->id,
        'block_type' => AccountBlockTypesEnum::RECEIVE->value,
        'paired_account_block_id' => $sendBlock->id,
        'amount' => '0',
    ]);

    ProcessBlockTransfers::run($receiveBlock);

    $sender->refresh();
    $receiver->refresh();

    expect($sender->qsr_balance)->toBe((string) (6 * NOM_DECIMALS))
        ->and($sender->qsr_sent)->toBe((string) (4 * NOM_DECIMALS))
        ->and($receiver->qsr_balance)->toBe((string) (4 * NOM_DECIMALS))
        ->and($receiver->qsr_received)->toBe((string) (4 * NOM_DECIMALS));
});

it('handles contract send blocks', function () {

    $token = load_token(NetworkTokensEnum::ZNN->zts());
    $sender = Account::factory()->create([
        'znn_balance' => (string) (10 * NOM_DECIMALS),
        'znn_sent' => '0',
    ]);
    $receiver = Account::factory()->create();

    $sender->tokens()->attach($token->id, [
        'balance' => (string) (10 * NOM_DECIMALS),
    ]);

    $block = AccountBlock::factory()->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'block_type' => AccountBlockTypesEnum::CONTRACT_SEND->value,
        'amount' => (string) (2 * NOM_DECIMALS),
    ]);

    ProcessBlockTransfers::run($block);

    $sender->refresh();

    expect($sender->znn_balance)->toBe((string) (8 * NOM_DECIMALS))
        ->and($sender->znn_sent)->toBe((string) (2 * NOM_DECIMALS));
});

it('handles contract receive blocks', function () {

    $token = load_token(NetworkTokensEnum::ZNN->zts());
    $sender = Account::factory()->create();
    $receiver = Account::factory()->create([
        'znn_balance' => '0',
        'znn_received' => '0',
    ]);

    $sendBlock = AccountBlock::factory()->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'block_type' => AccountBlockTypesEnum::SEND->value,
        'amount' => (string) (3 * NOM_DECIMALS),
    ]);

    $receiveBlock = AccountBlock::factory()->create([
        'account_id' => $receiver->id,
        'block_type' => AccountBlockTypesEnum::CONTRACT_RECEIVE->value,
        'paired_account_block_id' => $sendBlock->id,
        'amount' => '0',
    ]);

    ProcessBlockTransfers::run($receiveBlock);

    $receiver->refresh();

    expect($receiver->znn_balance)->toBe((string) (3 * NOM_DECIMALS))
        ->and($receiver->znn_received)->toBe((string) (3 * NOM_DECIMALS));
});

it('creates token balance entry when account has no prior balance for that token', function () {

    $token = load_token(NetworkTokensEnum::ZNN->zts());
    $sender = Account::factory()->create([
        'znn_balance' => (string) (5 * NOM_DECIMALS),
        'znn_sent' => '0',
    ]);
    $receiver = Account::factory()->create();

    $sender->tokens()->attach($token->id, [
        'balance' => (string) (5 * NOM_DECIMALS),
    ]);

    $sendBlock = AccountBlock::factory()->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'block_type' => AccountBlockTypesEnum::SEND->value,
        'amount' => (string) (2 * NOM_DECIMALS),
    ]);

    ProcessBlockTransfers::run($sendBlock);

    $sender->refresh();

    $senderBalance = $sender->tokens()
        ->where('token_id', $token->id)
        ->first();

    expect($senderBalance)->not->toBeNull()
        ->and($senderBalance->pivot->balance)->toBe((string) (3 * NOM_DECIMALS));
});
