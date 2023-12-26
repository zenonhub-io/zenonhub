<div wire:init="loadOverviewData">
    @if ($halted)
        <x-alert
            message="The bridge is currently halted, please wait until it is back online before interacting with it"
            type="warning"
            icon="exclamation-octagon"
            class="d-flex justify-content-center mb-4"
        />
    @elseif ($orchestrators < 66)
        <x-alert
            message="Only {{$orchestrators}}% of orchestrators are online, please wait until there are over 66% before interacting with the bridge"
            type="warning"
            icon="exclamation-octagon"
            class="d-flex justify-content-center mb-4"
        />
    @endif

    @if (! $halted && $orchestrators > 66)
        <x-alert
            type="success"
            class="mb-4 justify-content-center"
        >
            <div class="d-block d-md-flex justify-content-between align-items-center">
                <i class="bi bi-check-circle-fill lead me-3"></i>
                The bridge and orchestrators are online.
                <a href="{{ $affiliateLink }}" target="_blank" class="btn btn-outline-success ms-0 ms-md-3 d-block mt-3 mt-md-0">
                    Bridge tokens now
                    <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </x-alert>
    @endif

    <div class="bg-secondary shadow rounded-2 p-3">
        <div class="d-block d-md-flex justify-content-md-evenly">
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Bridge <span class="text-muted"><i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="Indicates if the bridge is online or has been halted"></i></span></span>
                <span class="float-end float-md-none">
                    <span class="legend-indicator bg-{{ (! $halted ? 'success' : 'danger') }}" data-bs-toggle="tooltip" data-bs-title="{{ (! $halted ? 'Active' : 'Halted') }}"></span>
                </span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Orchestrators <span class="text-muted"><i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="Over 66% of orchestrators need to be online for the bridge to function"></i></span></span>
                <span class="float-end float-md-none">
                    <span class="legend-indicator bg-{{ ($orchestrators > 66 ? 'success' : 'danger') }}" data-bs-toggle="tooltip" data-bs-title="{{ $orchestrators }}% Online"></span>
                </span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Admin <span class="text-muted"><i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="The admin address allowed to issue commands to the bridge"></i></span></span>
                <span class="float-end float-md-none">
                    <x-address :account="$adminAddress" :eitherSide="8" breakpoint="lg" :named="false"/>
                </span>
            </div>
        </div>
        <div class="d-block d-md-flex justify-content-md-evenly mt-2 pt-0 border-1 border-top-md mt-md-4 pt-md-4">
            <div class="text-start text-md-center mb-2 mb-md-0 order-0">
                <span class="d-inline d-md-block text-muted fs-sm">Total Inbound</span>
                <span class="float-end float-md-none pb-2">{{ $overview['totalUnwraps'] ?? '' }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0 order-0">
                <span class="d-inline d-md-block text-muted fs-sm">Total Outbound</span>
                <span class="float-end float-md-none pb-2">{{ $overview['totalWraps'] ?? '' }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0 order-0">
                <span class="d-inline d-md-block text-muted fs-sm">Tx Past 24h</span>
                <span class="float-end float-md-none pb-2">{{ $overview['dailyTxCount'] ?? '' }}</span>
            </div>
        </div>
    </div>
</div>
