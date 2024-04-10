<?php

declare(strict_types=1);

use App\Livewire\Profile\ManageApiTokens;
use App\Models\User;
use Livewire\Livewire;

test('api tokens can be created', function () {
    $this->actingAs($user = User::factory()->create()->fresh());

    $component = Livewire::test(ManageApiTokens::class)
        ->set(['createApiTokenForm' => [
            'name' => 'Test Token',
        ]])
        ->call('createApiToken')
        ->assertDispatched('profile.api-token.created')
        ->assertDispatched('show-inline-modal', id: 'view-api-token');

    expect($component->plainTextToken)->not->toBeNull();
    expect($user->fresh()->tokens)->toHaveCount(1);
    expect($user->fresh()->tokens->first())
        ->name->toEqual('Test Token');
})->group('profile', 'manage-api-tokens');

test('api tokens can be viewed', function () {
    $this->actingAs($user = User::factory()->create()->fresh());

    $user->tokens()->create([
        'name' => 'Test Token',
        'token' => Str::random(40),
    ]);

    Livewire::test(ManageApiTokens::class)
        ->assertSee('Test Token');

})->group('profile', 'manage-api-tokens');

test('api tokens can be deleted', function () {
    $this->actingAs($user = User::factory()->create()->fresh());

    $token = $user->tokens()->create([
        'name' => 'Test Token',
        'token' => Str::random(40),
    ]);

    Livewire::test(ManageApiTokens::class)
        ->call('confirmApiTokenDeletion', tokenId: $token->id)
        ->assertSet('apiTokenIdBeingDeleted', $token->id)
        ->assertDispatched('show-inline-modal', id: 'confirm-delete-token')
        ->call('deleteApiToken');

    expect($user->fresh()->tokens)->toHaveCount(0);
})->group('profile', 'manage-api-tokens');
