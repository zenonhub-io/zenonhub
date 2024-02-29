<?php

use App\Models\User;
use Livewire\Livewire;

$testComponent = new class extends \Livewire\Component
{
    use \App\Traits\Livewire\ConfirmsPasswordTrait;

    public function render()
    {
        return <<<'HTML'
            <div>
            </div>
            HTML;
    }
};

test('password confirmation can be started', function () use ($testComponent) {

    $user = User::factory()->create()->fresh();
    $this->actingAs($user);

    $confirmationId = 'test';

    $component = Livewire::test($testComponent::class)
        ->call('startConfirmingPassword', $confirmationId)
        ->assertDispatched('confirming-password');

    expect($component->confirmingPassword)->toBeTrue();
    expect($component->confirmableId)->toEqual($confirmationId);
    expect($component->confirmablePassword)->toBeEmpty();

})->group('profile', 'password-confirmation-trait');

test('password confirmation can be stopped', function () use ($testComponent) {

    $user = User::factory()->create()->fresh();
    $this->actingAs($user);

    $component = Livewire::test($testComponent::class)
        ->call('stopConfirmingPassword')
        ->assertDispatched('stop-confirming-password');

    expect($component->confirmingPassword)->toBeFalse();
    expect($component->confirmableId)->toBeNull();
    expect($component->confirmablePassword)->toBeEmpty();

})->group('profile', 'password-confirmation-trait');

test('password confirmation accepts correct password', function () use ($testComponent) {

    $user = User::factory()->create()->fresh();
    $this->actingAs($user);

    $confirmationId = 'test';

    Livewire::test($testComponent::class)
        ->set([
            'confirmableId' => $confirmationId,
            'confirmablePassword' => 'password',
        ])
        ->call('confirmPassword')
        ->assertDispatched('password-confirmed', id: $confirmationId)
        ->assertSessionHas('auth.password_confirmed_at')
        ->assertDispatched('stop-confirming-password');

})->group('profile', 'password-confirmation-trait');

test('password confirmation doesnt accept wrong password', function () use ($testComponent) {

    $user = User::factory()->create()->fresh();
    $this->actingAs($user);

    $confirmationId = 'test';

    Livewire::test($testComponent::class)
        ->set([
            'confirmableId' => $confirmationId,
            'confirmablePassword' => 'wrong-password',
        ])
        ->call('confirmPassword')
        ->assertHasErrors('confirmable_password')
        ->assertSessionMissing('auth.password_confirmed_at');

})->group('profile', 'password-confirmation-trait');
