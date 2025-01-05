@props(['title' => null, 'responsiveBorder' => true])

@php($borderClass = $responsiveBorder ? 'border-bottom-0 border-bottom-md' : 'border-bottom')

<header {{ $attributes->merge(['class' => 'header mb-6 '.$borderClass]) }}>
    @if ($title)
        <x-includes.header-title :title="$title" :centered="true" class="mb-4" />
    @endif
    {{ $slot }}
</header>
