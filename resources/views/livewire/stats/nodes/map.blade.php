<div wire:init="loadMapData">
    @if($readyToLoad)
        <div class="bg-secondary shadow rounded-2 mb-3 p-3">
            <div class="d-block d-md-flex justify-content-md-evenly">
                <div class="text-start text-md-center mb-2 mb-md-0">
                    <span class="d-inline d-md-block text-muted">{{ Str::plural('Node', $nodes['total'] ?? 0) }}</span>
                    <span class="float-end float-md-none">{{ $nodes['total'] ?? 0 }}</span>
                </div>
                <div class="text-start text-md-center mb-2 mb-md-0">
                    <span class="d-inline d-md-block text-muted">{{ Str::plural('City', $nodes['cities'] ?? 0) }}</span>
                    <span class="float-end float-md-none">{{ $nodes['cities'] ?? 0 }}</span>
                </div>
                <div class="text-start text-md-center">
                    <span class="d-inline d-md-block text-muted">{{ Str::plural('Country', $nodes['countries'] ?? 0) }}</span>
                    <span class="float-end float-md-none">{{ $nodes['countries'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="text-center">
            <canvas
                id="js-node-map"
                style="width:min(80vmin, 800px);height:min(80vmin, 800px);"
            ></canvas>
        </div>
    @else
        <x-alert
            message="Processing request..."
            type="info"
            icon="arrow-repeat spin"
            class="d-flex justify-content-center mb-0"
        />
    @endif
</div>
