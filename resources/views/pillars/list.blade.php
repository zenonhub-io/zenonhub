<x-app-layout>

    <x-slot:breadcrumbs>
        {{ Breadcrumbs::render('pillar.list') }}
    </x-slot>

    <x-includes.header :title="__('Pillars')" class="mb-4">
        <x-navigation.header.responsive-nav :items="[
            __('All') => route('pillar.list'),
            __('Active') => route('pillar.list', ['tab' => 'active']),
            __('Inactive') => route('pillar.list', ['tab' => 'inactive']),
            __('Revoked') => route('pillar.list', ['tab' => 'revoked'])
        ]" :active="$tab" />
    </x-includes.header>

    <livewire:pillars.pillar-list :tab="$tab" lazy />

</x-app-layout>

