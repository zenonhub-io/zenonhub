<x-app-layout>
    <x-includes.header :title="__('Pillars')" responsiveBorder>
        <x-navigation.header.responsive-nav :items="[
            __('All') => route('pillars'),
            __('Active') => route('pillars', ['tab' => 'active']),
            __('Inactive') => route('pillars', ['tab' => 'inactive']),
            __('Revoked') => route('pillars', ['tab' => 'revoked'])
        ]" :active="$tab" />
    </x-includes.header>

</x-app-layout>

