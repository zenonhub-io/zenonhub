<x-app-layout>
    <x-includes.header>
        <h1 class="ls-tight text-wrap text-break">
            {{ __('Momentums') }}
        </h1>
        <p class="text-sm text-muted mb-2">Showing the latest 50k results</p>
    </x-includes.header>
    <livewire:explorer.momentums-list lazy />
</x-app-layout>
