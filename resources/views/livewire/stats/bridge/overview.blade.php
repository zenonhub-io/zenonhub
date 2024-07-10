<div wire:init="loadOverviewData">
    <div class="bg-secondary shadow rounded-2 p-3 mb-4">
        <div class="d-block d-md-flex justify-content-md-evenly">
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Bridge <span class="text-muted"><i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="Indicates if the bridge is online or has been halted"></i></span></span>
                <span class="float-end float-md-none">
                    <span class="legend-indicator bg-{{ (! $halted ? 'success' : 'danger') }}" data-bs-toggle="tooltip" data-bs-title="{{ (! $halted ? 'Active' : 'Halted') }}"></span>
                </span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Orchestrators <span class="text-muted"><i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="Over {{ $requiredOrchestrators }}% of orchestrators need to be online for the bridge to function"></i></span></span>
                <span class="float-end float-md-none">
                    <span class="legend-indicator bg-{{ ($onlineOrchestrators > $requiredOrchestrators ? 'success' : 'danger') }}" data-bs-toggle="tooltip" data-bs-title="{{ $orchestrators }}% Online"></span>
                </span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Admin <span class="text-muted"><i class="bi-question-circle" data-bs-toggle="tooltip" data-bs-title="The admin address allowed to issue commands to the bridge"></i></span></span>
                <span class="float-end float-md-none">
                    <x-address :account="$adminAddress" :eitherSide="8" breakpoint="lg" :named="false"/>
                </span>
            </div>
        </div>
    </div>

    @if ($halted)
        <x-alert
            type="warning"
            icon="exclamation-octagon"
            class="d-flex justify-content-center mb-4">
            The bridge is currently halted
            @if($estimatedUnhaltMonemtum)
                for another {{ now()->addSeconds($estimatedUnhaltMonemtum * 10)->diffForHumans(['parts' => 2], true) }} ({{ number_format($estimatedUnhaltMonemtum) }} momentums)
            @endif
            please wait until it is back online before interacting with it.
        </x-alert>
    @elseif ($onlineOrchestrators < $requiredOrchestrators)
        <x-alert
            message="Only {{$onlineOrchestrators}}% of orchestrators are online, please wait until there are over {{ $requiredOrchestrators }}% before interacting with the bridge"
            type="warning"
            icon="exclamation-octagon"
            class="d-flex justify-content-center mb-4"
        />
    @endif

    @if (! $halted && $onlineOrchestrators > $requiredOrchestrators)
        <x-alert
            type="success"
            class="mb-4 justify-content-center"
        >
            <div class="d-block d-md-flex justify-content-between align-items-center">
                <i class="bi bi-check-circle-fill lead me-2 me-md-3"></i>
                The bridge and orchestrators are online
                <a href="{{ $affiliateLink }}" target="_blank" class="btn btn-outline-success ms-0 ms-md-3 d-block mt-3 mt-md-0">
                    Bridge tokens now
                    <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </x-alert>
    @endif

    @foreach($timeChallenges as $challenge)
        <x-alert
            message="A time challenge is in place for {{  $challenge['name'] }}. The challenge will expire in {{  number_format($challenge['endsIn']) }} momentums, {{ now()->addSeconds($challenge['endsIn'] * 10)->diffForHumans() }}"
            type="warning"
            icon="exclamation-octagon"
            class="d-flex justify-content-center mb-4"
        />
    @endforeach

    <div class="bg-secondary shadow rounded-2 p-3">

        <div class="form-group mb-4">
            <select id="form-request" class="form-select" wire:change="setDateRange($event.target.value)">
                <option value="all">All time</option>
                <option value="day">Past day</option>
                <option value="week">Past 7 days</option>
                <option value="month">Past 30 days</option>
                <option value="year">Past year</option>
            </select>
        </div>

        <div class="d-block d-md-flex justify-content-md-evenly">
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">ZNN Volume</span>
                <span class="float-end float-md-none pb-2">{{ $overview['znnVolume'] ?? '' }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">QSR Volume</span>
                <span class="float-end float-md-none pb-2">{{ $overview['qsrVolume'] ?? '' }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Inbound Tx</span>
                <span class="float-end float-md-none pb-2">{{ $overview['inboundTx'] ?? '' }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Outbound Tx</span>
                <span class="float-end float-md-none pb-2">{{ $overview['outboundTx'] ?? '' }}</span>
            </div>
        </div>
        <div class="d-block d-md-flex justify-content-md-evenly mt-2 pt-2 border-1 border-top mt-md-4 pt-md-4">
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Affiliate Tx</span>
                <span class="float-end float-md-none pb-2">{{ $overview['affiliateTx'] ?? '' }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Affiliate ZNN</span>
                <span class="float-end float-md-none pb-2">{{ $overview['affiliateZnn'] ?? '' }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Affiliate QSR</span>
                <span class="float-end float-md-none pb-2">{{ $overview['affiliateQsr'] ?? '' }}</span>
            </div>
        </div>
        <div class="d-block d-md-flex justify-content-md-evenly mt-2 pt-2 border-1 border-top mt-md-4 pt-md-4">
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Inbound ZNN</span>
                <span class="float-end float-md-none pb-2">{{ $overview['inboundZnn'] ?? '' }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Outbound ZNN</span>
                <span class="float-end float-md-none pb-2">{{ $overview['outboundZnn'] ?? '' }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Net ZNN Flow</span>
                <span class="float-end float-md-none pb-2">{{ $overview['netFlowZnn'] ?? '' }}</span>
            </div>
        </div>
        <div class="d-block d-md-flex justify-content-md-evenly mt-2 pt-2 border-1 border-top mt-md-4 pt-md-4">
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Inbound QSR</span>
                <span class="float-end float-md-none pb-2">{{ $overview['inboundQsr'] ?? '' }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Outbound QSR</span>
                <span class="float-end float-md-none pb-2">{{ $overview['outboundQsr'] ?? '' }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Net QSR Flow</span>
                <span class="float-end float-md-none pb-2">{{ $overview['netFlowQsr'] ?? '' }}</span>
            </div>
        </div>
    </div>
</div>
