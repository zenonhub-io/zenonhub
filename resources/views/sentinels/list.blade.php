<x-app-layout>
    <x-includes.header :title="__('Sentinels')" class="mb-4">
        <x-navigation.header.responsive-nav :items="[
            __('All') => route('sentinel.list'),
        ]" :active="$tab" />
    </x-includes.header>

    <livewire:sentinels.sentinel-list :tab="$tab" lazy />

</x-app-layout>

