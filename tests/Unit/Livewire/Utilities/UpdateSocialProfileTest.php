<?php

declare(strict_types=1);

use App\Livewire\Utilities\UpdateSocialProfile;
use App\Models\Nom\Pillar;
use App\Models\Nom\Token;
use App\Models\SocialProfile;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Nom\NetworkSeeder;
use Database\Seeders\TestGenesisSeeder;
use Livewire\Livewire;

uses()->group('livewire', 'utilities', 'update-social-profile');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NetworkSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

test('it loads the correct profile for a pillar', function () {
    $pillar = Pillar::factory()->create();
    $socialProfile = SocialProfile::factory()->create();

    $socialProfile->profileable()->associate($pillar);
    $socialProfile->save();

    Livewire::test(UpdateSocialProfile::class, [
        'itemId' => $pillar->slug,
        'itemType' => 'pillar',
    ])->assertSet('address', $pillar->owner->address)
        ->assertSet('socialProfile.id', $socialProfile->id)
        ->assertSet('socialProfileForm', $socialProfile->makeHidden('profileable')->toArray());
});

test('it loads the correct profile for an address', function () {
    $account = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    $socialProfile = SocialProfile::factory()->create();

    $socialProfile->profileable()->associate($account);
    $socialProfile->save();

    Livewire::test(UpdateSocialProfile::class, [
        'itemId' => $account->address,
        'itemType' => 'address',
    ])->assertSet('address', $account->address)
        ->assertSet('socialProfile.id', $socialProfile->id)
        ->assertSet('socialProfileForm', $socialProfile->makeHidden('profileable')->toArray());
});

test('it loads the correct profile for a token', function () {

    $token = Token::factory()->create();
    $socialProfile = SocialProfile::factory()->create();

    $socialProfile->profileable()->associate($token);
    $socialProfile->save();

    Livewire::test(UpdateSocialProfile::class, [
        'itemId' => $token->token_standard,
        'itemType' => 'token',
    ])->assertSet('address', $token->owner->address)
        ->assertSet('socialProfile.id', $socialProfile->id)
        ->assertSet('socialProfileForm', $socialProfile->makeHidden('profileable')->toArray());
});

test('it sets hasUserVerifiedAddress variable', function () {

    $this->actingAs($user = User::factory()->create()->fresh());
    $account = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    $pillar = Pillar::factory()->create([
        'owner_id' => $account,
    ]);

    $user->verifiedAccounts()->syncWithoutDetaching([
        $account->id => [
            'nickname' => 'Test address',
            'verified_at' => now(),
        ],
    ]);

    Livewire::test(UpdateSocialProfile::class, [
        'itemId' => $pillar->slug,
        'itemType' => 'pillar',
    ])->assertSet('hasUserVerifiedAddress', true);
});

test('it creates a new social profile', function () {
    $pillar = Pillar::factory()->create();
    Livewire::test(UpdateSocialProfile::class, [
        'itemId' => $pillar->slug,
        'itemType' => 'pillar',
    ]);

    expect($pillar->socialProfile()->get())->toHaveCount(1);
});

test('it updates a partial social profile via signature', function () {

    $account = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    $account->public_key = 'Pu6pBONZKbCMTZMYMrCKe8c59Rv/WOt2ZHUxuo9ifyY=';
    $account->save();

    $pillar = Pillar::factory()->create([
        'owner_id' => $account,
    ]);

    $profileData = [
        'avatar' => 'https://www.imagelink.com/avatar.com',
    ];

    Livewire::test(UpdateSocialProfile::class, [
        'itemId' => $pillar->slug,
        'itemType' => 'pillar',
    ])->set([
        'socialProfileForm' => $profileData,
        'message' => 'TEST',
        'signature' => '8d70ae934e6efda81e762088ce490328da886c1b9c22a2fd3cb53188cc972cc13c670d6da507b10cac0aa22148a0452c47c12ea20e72402cee5838aae3f35904',
    ])->call('saveProfile')
        ->assertDispatched('social-profile.updated');

    $socialProfile = $pillar->socialProfile;

    expect($socialProfile->bio)->toBeNull()
        ->and($socialProfile->avatar)->toEqual($profileData['avatar'])
        ->and($socialProfile->website)->toBeNull()
        ->and($socialProfile->email)->toBeNull()
        ->and($socialProfile->x)->toBeNull()
        ->and($socialProfile->telegram)->toBeNull()
        ->and($socialProfile->github)->toBeNull()
        ->and($socialProfile->medium)->toBeNull()
        ->and($socialProfile->discord)->toBeNull();
});

test('it updates an existing social profile via signature', function () {

    $account = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    $account->public_key = 'Pu6pBONZKbCMTZMYMrCKe8c59Rv/WOt2ZHUxuo9ifyY=';
    $account->save();

    $pillar = Pillar::factory()->create([
        'owner_id' => $account,
    ]);

    $profileData = [
        'bio' => 'New bio',
        'avatar' => 'https://www.imagelink.com/avatar.com',
        'website' => 'https://example.com',
        'email' => 'test@example.com',
        'x' => 'https://x.com/example',
        'telegram' => 'https://t.me/example',
        'github' => 'https://github.com/example',
        'medium' => 'https://medium.com/example',
        'discord' => 'https://discord.com/example',
    ];

    Livewire::test(UpdateSocialProfile::class, [
        'itemId' => $pillar->slug,
        'itemType' => 'pillar',
    ])->set([
        'socialProfileForm' => $profileData,
        'message' => 'TEST',
        'signature' => '8d70ae934e6efda81e762088ce490328da886c1b9c22a2fd3cb53188cc972cc13c670d6da507b10cac0aa22148a0452c47c12ea20e72402cee5838aae3f35904',
    ])->call('saveProfile')
        ->assertDispatched('social-profile.updated');

    $socialProfile = $pillar->socialProfile;

    expect($socialProfile->bio)->toEqual($profileData['bio'])
        ->and($socialProfile->avatar)->toEqual($profileData['avatar'])
        ->and($socialProfile->website)->toEqual($profileData['website'])
        ->and($socialProfile->email)->toEqual($profileData['email'])
        ->and($socialProfile->x)->toEqual($profileData['x'])
        ->and($socialProfile->telegram)->toEqual($profileData['telegram'])
        ->and($socialProfile->github)->toEqual($profileData['github'])
        ->and($socialProfile->medium)->toEqual($profileData['medium'])
        ->and($socialProfile->discord)->toEqual($profileData['discord']);
});

test('it updates an existing social profile via user verified accounts', function () {

    $this->actingAs($user = User::factory()->create()->fresh());
    $account = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    $pillar = Pillar::factory()->create([
        'owner_id' => $account,
    ]);

    $user->verifiedAccounts()->syncWithoutDetaching([
        $account->id => [
            'nickname' => 'Test address',
            'verified_at' => now(),
        ],
    ]);

    $profileData = [
        'bio' => 'New bio',
        'avatar' => 'https://www.imagelink.com/avatar.com',
        'website' => 'https://example.com',
        'email' => 'test@example.com',
        'x' => 'https://x.com/example',
        'telegram' => 'https://t.me/example',
        'github' => 'https://github.com/example',
        'medium' => 'https://medium.com/example',
        'discord' => 'https://discord.com/example',
    ];

    Livewire::test(UpdateSocialProfile::class, [
        'itemId' => $pillar->slug,
        'itemType' => 'pillar',
    ])->set([
        'socialProfileForm' => $profileData,
    ])->call('saveProfile')
        ->assertDispatched('social-profile.updated');

    $socialProfile = $pillar->socialProfile;

    expect($socialProfile->bio)->toEqual($profileData['bio'])
        ->and($socialProfile->avatar)->toEqual($profileData['avatar'])
        ->and($socialProfile->website)->toEqual($profileData['website'])
        ->and($socialProfile->email)->toEqual($profileData['email'])
        ->and($socialProfile->x)->toEqual($profileData['x'])
        ->and($socialProfile->telegram)->toEqual($profileData['telegram'])
        ->and($socialProfile->github)->toEqual($profileData['github'])
        ->and($socialProfile->medium)->toEqual($profileData['medium'])
        ->and($socialProfile->discord)->toEqual($profileData['discord']);
});

test('it doesnt run for invalid address signatures', function () {
    $account = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    $account->public_key = 'Pu6pBONZKbCMTZMYMrCKe8c59Rv/WOt2ZHUxuo9ifyY=';
    $account->save();

    $pillar = Pillar::factory()->create([
        'owner_id' => $account,
    ]);

    Livewire::test(UpdateSocialProfile::class, [
        'itemId' => $pillar->slug,
        'itemType' => 'pillar',
    ])->set([
        'socialProfileForm' => [
            'bio' => 'New bio',
            'avatar' => 'https://www.imagelink.com/avatar.com',
            'website' => 'https://example.com',
            'email' => 'test@example.com',
            'x' => 'https://x.com/example',
            'telegram' => 'https://t.me/example',
            'github' => 'https://github.com/example',
            'medium' => 'https://medium.com/example',
            'discord' => 'https://discord.com/example',
        ],
        'message' => 'INVALID',
        'signature' => '8d70ae934e6efda81e762088ce490328da886c1b9c22a2fd3cb53188cc972cc13c670d6da507b10cac0aa22148a0452c47c12ea20e72402cee5838aae3f35904',
    ])->call('saveProfile')
        ->assertHasErrors('signature')
        ->assertNotDispatched('social-profile.updated');

});
