<x-svg file="logo" class="logo-light h-rem-10 text-primary "/>
<div class="text-nowrap ms-3">
    <x-link :href="route('home')" class="h4 mb-0 fw-bolder">
        {{ config('app.name') }}
    </x-link>
    {{ $breadcrumbs ?? '' }}
</div>

