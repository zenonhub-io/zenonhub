@props(['title' => null, 'responsiveBorder' => true])

@php($borderClass = $responsiveBorder ? 'border-bottom-0 border-bottom-md' : 'border-bottom')

<header {{ $attributes->merge(['class' => 'header mb-6 mx-3 mx-lg-6 '.$borderClass]) }}>
     @if ($title)
         <x-includes.header-title :title="$title" class="mb-4" />
    @endif
    {{ $slot }}
</header>
