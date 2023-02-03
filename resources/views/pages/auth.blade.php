<x-layouts.app>
    <x-slot name="pageTitle">
        {{ $meta['title'] }}
    </x-slot>
    <x-slot name="pageMetaTags">
        <meta name="description" content="{{ $meta['description'] }}">
        <meta name="og:description" content="{{ $meta['description'] }}">
    </x-slot>

    <div class="container">
        <div class="row">
            <div class="col-24 col-md-16 offset-md-4 col-lg-12 offset-lg-6">
                <x-dynamic-component :component="$data['component']" :data="$data" />
            </div>
        </div>
    </div>
</x-layouts.app>
