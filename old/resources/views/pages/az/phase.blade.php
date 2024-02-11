<x-layouts.app>
    <x-slot name="breadcrumbs">
        {{ Breadcrumbs::render('phase', $phase) }}
    </x-slot>
    <div class="container">
        <div class="row">
            <div class="col-24">
                <livewire:az.phase :hash="$phase->hash"/>
            </div>
        </div>
    </div>
</x-layouts.app>
