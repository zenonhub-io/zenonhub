<x-layouts.app>
    <x-slot name="pageTitle">
        {{ $meta['title'] }}
    </x-slot>
    <x-slot name="pageMetaTags">
        <meta name="description" content="{{ $meta['description'] }}">
        <meta name="og:description" content="{{ $meta['description'] }}">
    </x-slot>
    <x-slot name="pageBreadcrumbs">
        {{ Breadcrumbs::render('project', $data['project']) }}
    </x-slot>

    <div class="container">
        <div class="row">
            <div class="col-24">
                <livewire:az.project :hash="$data['project']->hash"/>
            </div>
        </div>
    </div>
</x-layouts.app>
