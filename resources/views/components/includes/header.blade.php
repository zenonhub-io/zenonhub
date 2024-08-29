@props(['title' => null])

<header {{ $attributes->merge(['class' => 'header mb-6 mx-3 mx-lg-6 border-bottom']) }}>
     @if ($title)
         <x-includes.header-title :title="$title" class="mb-4" />
    @endif
    {{ $slot }}
</header>
