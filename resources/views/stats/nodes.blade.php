<x-app-layout>

    <x-includes.header :title="__('Node Stats')" class="mb-4">
        <x-navigation.header.responsive-nav :items="[
            __('Overview') => route('stats.public-nodes'),
            __('Countries') => route('stats.public-nodes', ['tab' => 'countries']),
            __('Networks') => route('stats.public-nodes', ['tab' => 'networks']),
            __('Versions') => route('stats.public-nodes', ['tab' => 'versions']),
            __('Historic') => route('stats.public-nodes', ['tab' => 'historic']),
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'overview')
    @endif

    @if ($tab === 'countries')
    @endif

    @if ($tab === 'networks')
    @endif

    @if ($tab === 'versions')
    @endif

    @if ($tab === 'historic')
    @endif

</x-app-layout>
