<x-app-layout>
    <x-includes.header :responsive-border="false">
        <h1 class="ls-tight text-wrap text-break">
            {{ __('Account Blocks') }}
        </h1>
        <p class="text-sm text-muted mb-2">Showing the latest 50k results</p>
    </x-includes.header>
    <livewire:explorer.account-block-list lazy />
</x-app-layout>
