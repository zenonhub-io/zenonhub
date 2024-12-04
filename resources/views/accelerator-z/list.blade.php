<x-app-layout>
    <x-includes.header :title="__('Accelerator Z')" class="mb-4">
        <x-navigation.header.responsive-nav :items="[
            __('All') => route('accelerator-z.list'),
            __('Open') => route('accelerator-z.list', ['tab' => 'open']),
            __('Accepted') => route('accelerator-z.list', ['tab' => 'accepted']),
            __('Completed') => route('accelerator-z.list', ['tab' => 'completed']),
            __('Rejected') => route('accelerator-z.list', ['tab' => 'rejected']),
        ]" :active="$tab" />
    </x-includes.header>
    <livewire:accelerator-z.project-list :tab="$tab" lazy />
</x-app-layout>

