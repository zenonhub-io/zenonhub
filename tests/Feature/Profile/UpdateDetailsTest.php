<?php

declare(strict_types=1);

use App\Livewire\Profile\UpdateDetails;
use App\Models\User;
use Livewire\Livewire;

test('current profile details are available', function () {
    $this->actingAs($user = User::factory()->create());

    $component = Livewire::test(UpdateDetails::class);

    expect($component->state['name'])->toEqual($user->name);
    expect($component->state['username'])->toEqual($user->username);
    expect($component->state['email'])->toEqual($user->email);
})->group('profile', 'details');

test('profile details can be updated', function () {
    $this->actingAs($user = User::factory()->create());

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
})->group('profile', 'details');