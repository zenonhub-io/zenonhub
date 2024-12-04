<x-app-layout>
    <x-includes.header :title="__('Accounts')">
        <x-navigation.header.responsive-nav :items="[
            __('All') => route('explorer.account.list'),
            __('Contracts') => route('explorer.account.list', ['tab' => 'contracts']),
            __('Pillars') => route('explorer.account.list', ['tab' => 'pillars']),
            __('Sentinels') => route('explorer.account.list', ['tab' => 'sentinels']),
        ]" :active="$tab" />
    </x-includes.header>
    <livewire:explorer.accounts-list :tab="$tab" lazy />
</x-app-layout>
