<x-app-layout>
    <x-includes.header :title="__('Tokens')">
        <x-navigation.header.responsive-nav :items="[
            __('All') => route('explorer.token.list'),
            __('Network') => route('explorer.token.list', ['tab' => 'network']),
            __('User') => route('explorer.token.list', ['tab' => 'user']),
        ]" :active="$tab" />
    </x-includes.header>
    <livewire:explorer.token-list :tab="$tab" lazy />
</x-app-layout>
