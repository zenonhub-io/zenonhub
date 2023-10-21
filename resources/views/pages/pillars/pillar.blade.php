<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <x-slot name="breadcrumbs">
        {{ Breadcrumbs::render('pillar', $data['pillar']) }}
    </x-slot>
    <div class="container">
        <div class="row">
            <div class="col-24">
                <livewire:pillars.pillar :slug="$data['pillar']->slug"/>
            </div>
        </div>
    </div>
</x-layouts.app>
