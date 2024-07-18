<?php

declare(strict_types=1);

use App\Livewire\Profile\ManageAddresses;
use App\Models\User;
use Database\Seeders\Nom\ChainsSeeder;
use Livewire\Livewire;

uses()->group('profile', 'manage-addresses');

beforeEach(function () {
    $this->seed(ChainsSeeder::class);

    $account = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    $account->public_key = 'Pu6pBONZKbCMTZMYMrCKe8c59Rv/WOt2ZHUxuo9ifyY=';
    $account->save();
});

test('verified address can be added', function () {
    $this->actingAs($user = User::factory()->create()->fresh());

    Livewire::test(ManageAddresses::class)
        ->set(['verifyAddressForm' => [
            'address' => 'z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm',
            'nickname' => 'Test Address',
            'message' => 'TEST',
            'signature' => '8d70ae934e6efda81e762088ce490328da886c1b9c22a2fd3cb53188cc972cc13c670d6da507b10cac0aa22148a0452c47c12ea20e72402cee5838aae3f35904',
        ]])
        ->call('verifyAddress')
        ->assertDispatched('profile.address.verified');

    expect($user->fresh()->verifiedAccounts)->toHaveCount(1);
    expect($user->fresh()->verifiedAccounts->first()->pivot->nickname)->toEqual('Test Address');
    expect($user->fresh()->verifiedAccounts->first())->address->toEqual('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
});

test('verified address can be viewed', function () {
    $this->actingAs($user = User::factory()->create()->fresh());

    $account = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    $user->verifiedAccounts()->attach([
        $account->id => [
            'nickname' => 'Test Address',
            'verified_at' => now(),
        ],
    ]);

    Livewire::test(ManageAddresses::class)
        ->assertSee('Test Address')
        ->assertSee('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
});

test('invalid signatures are rejected', function () {
    $this->actingAs($user = User::factory()->create()->fresh());

    Livewire::test(ManageAddresses::class)
        ->set(['verifyAddressForm' => [
            'address' => 'z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm',
            'nickname' => 'Test Address',
            'message' => 'TEST',
            'signature' => 'invalid',
        ]])
        ->call('verifyAddress')
        ->assertHasErrors('signature');

    expect($user->fresh()->verifiedAccounts)->toHaveCount(0);
});

test('addresses can be deleted', function () {
    $this->actingAs($user = User::factory()->create()->fresh());

    $account = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    $user->verifiedAccounts()->attach([
        $account->id => [
            'nickname' => 'Test Address',
            'verified_at' => now(),
        ],
    ]);

    Livewire::test(ManageAddresses::class)
        ->call('confirmAddressDeletion', address: $account->address)
        ->assertSet('addressBeingDeleted', $account->address)
        ->assertDispatched('show-inline-modal', id: 'confirm-delete-address')
        ->call('deleteAddress');

    expect($user->fresh()->verifiedAccounts)->toHaveCount(0);
});
