<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <x-slot name="breadcrumbs">
        {{ Breadcrumbs::render('pillars') }}
    </x-slot>
    <div class="container mb-4 js-scroll-to">
        <div class="row">
            <div class="col-24">
                <livewire:pillars.overview />
            </div>
        </div>
    </div>
</x-layouts.app>
