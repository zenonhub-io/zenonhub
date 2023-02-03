<x-layouts.app>
    <x-slot name="pageTitle">
        {{ $meta['title'] }}
    </x-slot>
    <x-slot name="pageMetaTags">
        <meta name="description" content="{{ $meta['description'] }}">
        <meta name="og:description" content="{{ $meta['description'] }}">
    </x-slot>
    <x-slot name="pageBreadcrumbs">
        {{ Breadcrumbs::render('az') }}
    </x-slot>

    <div class="container mb-4 js-scroll-to">
        <div class="row">
            <div class="col-24">
                <livewire:az.overview />
            </div>
        </div>
    </div>
</x-layouts.app>
