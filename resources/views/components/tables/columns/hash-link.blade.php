@props([
    'link',
    'hash',
    'navigate' => true,
    'newTab' => false,
    'alwaysShort' => false,
    'breakpoint' => 'md'
])

<x-link :href="$link" :navigate="$navigate" :new-tab="$newTab">
    <x-hash :hash="$hash" :always-short="$alwaysShort" :breakpoint="$breakpoint" />
</x-link>


