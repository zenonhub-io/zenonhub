<?php

declare(strict_types=1);

use App\Livewire\Profile\UpdateDetails;
use App\Models\User;
use Livewire\Livewire;

uses()->group('profile', 'details');

test('current profile details are available', function () {
    $this->actingAs($user = User::factory()->create()->fresh());

    $component = Livewire::test(UpdateDetails::class);

    expect($component->state['name'])->toEqual($user->name);
    expect($component->state['username'])->toEqual($user->username);
    expect($component->state['email'])->toEqual($user->email);
});

test('profile details can be updated with different email', function () {
    $this->actingAs($user = User::factory()->create()->fresh());

    Livewire::test(UpdateDetails::class)
        ->set('state', [
            'name' => 'Test Name',
            'username' => 'test',
            'email' => 'test@example.com',
        ])
        ->call('updateProfileInformation')
        ->assertDispatched('profile.details.saved');

    expect($user->fresh())
        ->name->toEqual('Test Name')
        ->username->toEqual('test')
        ->email->toEqual('test@example.com');
});

test('profile details can be updated with same email', function () {
    $this->actingAs($user = User::factory()->create()->fresh());

    Livewire::test(UpdateDetails::class)
        ->set('state', [
            'name' => 'Test Name',
            'username' => 'test',
            'email' => $user->email,
        ])
        ->call('updateProfileInformation')
        ->assertDispatched('profile.details.saved');

    expect($user->fresh())
        ->name->toEqual('Test Name')
        ->username->toEqual('test')
        ->email->toEqual($user->email);
});
