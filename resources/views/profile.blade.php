<x-app-layout>

    <x-includes.header :title="__('Your Profile')" responsiveBorder>
        <x-navigation.header.responsive-nav :items="[
            __('Details') => route('profile', ['tab' => 'details']),
            __('Security') => route('profile', ['tab' => 'security']),
            __('Notifications') => route('profile', ['tab' => 'notifications']),
            __('Favorites') => route('profile', ['tab' => 'favorites']),
            __('Addresses') => route('profile', ['tab' => 'addresses']),
            __('API Keys') => route('profile', ['tab' => 'api-keys'])
        ]" :active="$tab" />
    </x-includes.header>

    @if($tab === 'details')
        @livewire('profile.update-details')
        <hr class="my-6">
        @livewire('profile.update-password')
    @endif

    @if($tab === 'security')
        @livewire('profile.manage-two-factor-authentication')
        <hr class="my-6">
        @livewire('profile.delete-user')
    @endif

    @if($tab === 'notifications')
        @livewire('profile.manage-notifications')
    @endif

    @if($tab === 'favorites')

    @endif

    @if($tab === 'addresses')

    @endif

    @if($tab === 'api-keys')
        @livewire('profile.manage-api-tokens')
    @endif
</x-app-layout>