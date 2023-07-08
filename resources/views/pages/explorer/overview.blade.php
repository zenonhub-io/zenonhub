<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <x-slot name="pageBreadcrumbs">
        {{ Breadcrumbs::render($data['component'] ?? 'explorer') }}
    </x-slot>
    <div class="container mb-4">
        <div class="row">
            <div class="col-24">
                <livewire:explorer.search key="{{now()}}" />
                @if (isset($data['component']))
                    <livewire:is :component="$data['component']" key="{{now()}}" />
                @else
                    <livewire:explorer.overview key="{{now()}}" />
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
