<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <x-slot name="pageBreadcrumbs">
        {{ Breadcrumbs::render('phase', $data['phase']) }}
    </x-slot>
    <div class="container">
        <div class="row">
            <div class="col-24">
                <livewire:az.phase :hash="$data['phase']->hash"/>
            </div>
        </div>
    </div>
</x-layouts.app>
