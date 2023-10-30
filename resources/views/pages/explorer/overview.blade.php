<x-layouts.app>
    <x-slot name="breadcrumbs">
        {{ Breadcrumbs::render($view ?? 'explorer') }}
    </x-slot>
    <div class="container mb-4">
        <div class="row">
            <div class="col-24">
                <livewire:explorer.search key="{{now()}}" />
                @if (isset($view))
                    <livewire:is :component="$view" key="{{now()}}" />
                @else
                    <livewire:explorer.overview key="{{now()}}" />
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
