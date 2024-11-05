<x-app-layout>

    <x-includes.header :title="__('Accelerator-Z Stats')" class="mb-4">
        <x-navigation.header.responsive-nav :items="[
            __('Overview') => route('stats.accelerator-z'),
            __('Engagement') => route('stats.accelerator-z', ['tab' => 'engagement']),
            __('Contributors') => route('stats.accelerator-z', ['tab' => 'contributors']),
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'overview')
    @endif

    @if ($tab === 'engagement')
    @endif

    @if ($tab === 'contributors')
    @endif

</x-app-layout>
