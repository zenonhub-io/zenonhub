<?php

use App\Livewire\Profile\ManageNotifications;
use App\Models\User;
use Livewire\Livewire;

test('component displays notification subscriptions', function () {
    $this->seed(\Database\Seeders\NotificationTypesSeeder::class);
    $this->actingAs(User::factory()->create());

    Livewire::test(ManageNotifications::class)
        ->assertSee('Site News')
        ->assertSee('Important Zenon Hub updates and news');

})->group('profile', 'manage-notifications');

test('user can subscribe to notifications', function () {
    $this->seed(\Database\Seeders\NotificationTypesSeeder::class);
    $this->actingAs($user = User::factory()->create());

    expect($user->fresh()->notificationTypes)->toHaveCount(0);

    Livewire::test(ManageNotifications::class)
        ->set(['notifications' => [
            '1' => true,
        ]])
        ->call('updateNotificationSubscriptions')
        ->assertDispatched('profile.notifications.saved');

    expect($user->fresh()->notificationTypes)->toHaveCount(1);

})->group('profile', 'manage-notifications');

test('user can unsubscribe from notifications', function () {
    $this->seed(\Database\Seeders\NotificationTypesSeeder::class);
    $this->actingAs($user = User::factory()->withNotificationSubscription(2)->create());

    expect($user->fresh()->notificationTypes)->toHaveCount(2);

    Livewire::test(ManageNotifications::class)
        ->set(['notifications' => [
            '1' => false,
            '2' => false,
        ]])
        ->call('updateNotificationSubscriptions')
        ->assertDispatched('profile.notifications.saved');

    expect($user->fresh()->notificationTypes)->toHaveCount(0);

})->group('profile', 'manage-notifications');

test('user can modify notification subscriptions', function () {
    $this->seed(\Database\Seeders\NotificationTypesSeeder::class);
    $this->actingAs($user = User::factory()->withNotificationSubscription(2)->create());

    expect($user->fresh()->notificationTypes)->toHaveCount(2);

    Livewire::test(ManageNotifications::class)
        ->set(['notifications' => [
            '1' => true,
            '2' => false,
            '3' => true,
        ]])
        ->call('updateNotificationSubscriptions')
        ->assertDispatched('profile.notifications.saved');

    expect($user->fresh()->notificationTypes)->toHaveCount(2);
    expect($user->fresh()->notificationTypes->pluck('id'))->toMatchArray([1, 3]);

})->group('profile', 'manage-notifications');
