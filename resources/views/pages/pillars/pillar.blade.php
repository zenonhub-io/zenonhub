<x-layouts.app>
    <x-slot name="breadcrumbs">
        {{ Breadcrumbs::render('pillar', $pillar) }}
    </x-slot>
    <div class="container">
        <div class="row">
            <div class="col-24">
                <livewire:pillars.pillar :slug="$pillar->slug"/>
            </div>
        </div>
    </div>
</x-layouts.app>
