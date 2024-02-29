<?php

use App\Livewire\Profile\ManageTwoFactorAuthentication;
use App\Models\User;
use Laravel\Fortify\Features;
use Livewire\Livewire;

test('two factor authentication can be enabled', function () {
    $this->actingAs($user = User::factory()->create());

    $this->withSession(['auth.password_confirmed_at' => time()]);

    Livewire::test(ManageTwoFactorAuthentication::class)
        ->call('enableTwoFactorAuthentication');

    $user = $user->fresh();

    expect($user->two_factor_secret)->not->toBeNull();
    expect($user->recoveryCodes())->toHaveCount(8);
})->skip(fn () => ! Features::canManageTwoFactorAuthentication(), 'Two factor authentication is not enabled.')->group('profile', '2fa');

test('recovery codes can be regenerated', function () {
    $this->actingAs($user = User::factory()->create());

    $this->withSession(['auth.password_confirmed_at' => time()]);

    $component = Livewire::test(ManageTwoFactorAuthentication::class)
        ->call('enableTwoFactorAuthentication')
        ->call('regenerateRecoveryCodes');

    $user = $user->fresh();

    $component->call('regenerateRecoveryCodes');

    expect($user->recoveryCodes())->toHaveCount(8);
    expect(array_diff($user->recoveryCodes(), $user->fresh()->recoveryCodes()))->toHaveCount(8);
})->skip(fn () => ! Features::canManageTwoFactorAuthentication(), 'Two factor authentication is not enabled.')->group('profile', '2fa');

test('two factor authentication can be disabled', function () {
    $this->actingAs($user = User::factory()->create());

    $this->withSession(['auth.password_confirmed_at' => time()]);

    $component = Livewire::test(ManageTwoFactorAuthentication::class)
        ->call('enableTwoFactorAuthentication');

    $this->assertNotNull($user->fresh()->two_factor_secret);

    $component->call('disableTwoFactorAuthentication');

    expect($user->fresh()->two_factor_secret)->toBeNull();
})->skip(fn () => ! Features::canManageTwoFactorAuthentication(), 'Two factor authentication is not enabled.')->group('profile', '2fa');
