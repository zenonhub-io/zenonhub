<x-app-layout>
    <x-includes.header :title="__('Accounts')">
        <x-navigation.header.responsive-nav :items="[
            __('All') => route('explorer.account.list'),
            __('Contracts') => route('explorer.account.list', ['tab' => 'contracts']),
            __('Pillars') => route('explorer.account.list', ['tab' => 'pillars']),
            __('Sentinels') => route('explorer.account.list', ['tab' => 'sentinels']),
            __('Favorites') => route('explorer.account.list', ['tab' => 'favorites']),
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab !== 'favorites')
        <livewire:explorer.accounts-list :tab="$tab" lazy />
    @else
        @auth
            <livewire:explorer.accounts-list :tab="$tab" lazy />
        @else
            <x-cards.card class="mx-3 mx-md-6">
                <x-cards.body>
                    <i class="bi bi-info-circle-fill me-2"></i> {!! __(':loginLink to save your favorite accounts', [
                    'loginLink' => '<a href="'.route('login', ['redirect' => url()->current()]).'">'.__('Login').'</a>',
                ]) !!}
                </x-cards.body>
            </x-cards.card>
        @endauth
    @endif
</x-app-layout>
