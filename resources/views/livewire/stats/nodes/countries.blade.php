<div wire:init="loadCountriesData">
    @if($readyToLoad)
        <div id="chart-node-countries" class="mb-3"></div>
    @else
        <x-alert
            message="Processing request..."
            type="info"
            icon="arrow-repeat spin"
            class="d-flex justify-content-center mb-0"
        />
    @endif
</div>
