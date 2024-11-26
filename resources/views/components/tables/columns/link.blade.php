@props(['link', 'navigate' => true, 'newTab' => false])

<x-link :href="$link" :navigate="$navigate" :new-tab="$newTab">{{ $text }}</x-link>
