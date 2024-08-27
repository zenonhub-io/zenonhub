<x-app-layout>
    <x-includes.header :title="$pillar->slug" responsiveBorder>
        <x-navigation.header.responsive-nav :items="[
            __('Delegators') => route('pillar.detail', ['slug' => $pillar->slug, 'tab' => 'delegators']),
            __('Votes') => route('pillar.detail', ['slug' => $pillar->slug, 'tab' => 'votes']),
            __('Updates') => route('pillar.detail', ['slug' => $pillar->slug, 'tab' => 'updates']),
            __('JSON') => route('pillar.detail', ['slug' => $pillar->slug, 'tab' => 'json'])
        ]" :active="$tab" />
    </x-includes.header>
</x-app-layout>

