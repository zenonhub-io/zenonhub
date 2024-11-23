<x-app-layout>
    <x-includes.header :title="__('Bridge')">
        <x-navigation.header.responsive-nav :items="[
            __('Inbound') => route('explorer.bridge.list'),
            __('Outbound') => route('explorer.bridge.list', ['tab' => 'outbound']),
            __('ETH LP') => route('explorer.bridge.list', ['tab' => 'eth-lp']),
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'inbound')

    @endif

    @if ($tab === 'outbound')

    @endif

    @if ($tab === 'eth-lp')
        <livewire:explorer.eth-lp-staking-list />
    @endif

</x-app-layout>
