<x-app-layout>
    <x-includes.header>
        <h1 class="ls-tight text-wrap text-break">
            {{ __('Transactions') }}
        </h1>
        <p class="text-sm text-muted mb-2">Showing the latest 50k results</p>
    </x-includes.header>
    <livewire:explorer.transaction-list lazy />
</x-app-layout>
