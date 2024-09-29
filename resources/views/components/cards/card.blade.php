@props(['heading', 'body', 'content' => null])

<div {{ $attributes->merge(['class' => 'card shadow']) }}>

    @isset($heading)
        <x-cards.heading>
            {{ $heading }}
        </x-cards.heading>
    @endisset

    @isset($body)
        <x-cards.body>
            {{ $body }}
        </x-cards.body>
    @endisset

    {{ $slot->isEmpty() ? $content : $slot }}
</div>
