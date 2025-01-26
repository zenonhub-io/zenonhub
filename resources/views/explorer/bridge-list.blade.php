<x-app-layout>
    <x-includes.header :title="__('Bridge')">
        <x-navigation.header.responsive-nav :items="[
            __('Inbound') => route('explorer.bridge.list'),
            __('Outbound') => route('explorer.bridge.list', ['tab' => 'outbound']),
            __('Networks') => route('explorer.bridge.list', ['tab' => 'networks']),
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'inbound')
        <livewire:explorer.bridge.inbound-list lazy />
    @endif

    @if ($tab === 'outbound')
        <livewire:explorer.bridge.outbound-list lazy />
    @endif

    @if ($tab === 'networks')
        <livewire:explorer.bridge.network-list lazy />
    @endif

</x-app-layout>
