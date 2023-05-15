<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
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
