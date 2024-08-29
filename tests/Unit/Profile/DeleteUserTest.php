<?php

declare(strict_types=1);

use App\Livewire\Profile\DeleteUser;
use App\Models\User;
use Livewire\Livewire;

uses()->group('profile', 'delete-account');

test('user accounts can be deleted', function () {
    $this->actingAs($user = User::factory()->create()->fresh());

    $this->withSession(['auth.password_confirmed_at' => time()]);

    Livewire::test(DeleteUser::class)
        ->call('deleteUser');

    expect($user->fresh())->toBeNull();
});
