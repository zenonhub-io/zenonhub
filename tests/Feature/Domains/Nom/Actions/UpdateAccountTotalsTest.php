<?php

declare(strict_types=1);

use App\Domains\Nom\Actions\UpdateAccountTotals;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;

uses()->group('nom', 'nom-actions', 'update-account-totals');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

it('updates an accounts current balance', function () {

    $token = load_token(NetworkTokensEnum::ZNN->value);

    $sender = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    $sender->genesis_znn_balance = (string) (5 * NOM_DECIMALS);
    $sender->save();

    $receiver = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvm1');

    AccountBlock::factory()->count(5)->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'amount' => (string) (1 * NOM_DECIMALS),
    ]);

    (new UpdateAccountTotals)->handle($sender);
    (new UpdateAccountTotals)->handle($receiver);

    $receiver->fresh();
    $receiverBalance = $receiver->balances()
        ->where('token_id', $token->id)
        ->first()
        ->pivot
        ->balance;

    $sender->fresh();
    $senderBalance = $sender->balances()
        ->where('token_id', $token->id)
        ->first()
        ->pivot
        ->balance;

    expect($receiverBalance)->toBe((string) (5 * NOM_DECIMALS))
        ->and($senderBalance)->toBe('0');
});
