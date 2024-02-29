@props(['title', 'content'])

<div {{ $attributes->merge(['class' => 'card shadow']) }}>
    @isset($title)
        <x-cards.heading :title="$title" />
    @endisset
    <div class="card-body">
        {{ $slot->isEmpty() ? $content : $slot }}
    </div>
</div>
