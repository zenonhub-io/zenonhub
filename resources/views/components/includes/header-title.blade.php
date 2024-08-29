@props(['title' => null])

<div {{ $attributes->merge(['class' => 'row align-items-center']) }}>
    <div class="col">
        <h1 class="ls-tight">{{ $title }}</h1>
    </div>
</div>
