<div wire:init="loadLiquidityData">
    <div class="bg-secondary shadow rounded-2 p-3 mb-4">
        <div class="d-block d-md-flex justify-content-md-evenly">
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Status</span>
                <span class="float-end float-md-none">
                    <span class="legend-indicator bg-{{ (! $halted ? 'success' : 'danger') }}" data-bs-toggle="tooltip" data-bs-title="{{ (! $halted ? 'Active' : 'Halted') }}"></span>
                </span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">Admin</span>
                <span class="float-end float-md-none">
                    <x-address :account="$adminAddress" :eitherSide="10" breakpoint="lg"/>
                </span>
            </div>
        </div>
        <div class="d-block d-md-flex justify-content-md-evenly mt-2 pt-0 border-1 border-top-md mt-md-4 pt-md-4">
            <div class="text-start text-md-center mb-2 mb-md-0 order-0">
                <span class="d-inline d-md-block text-muted fs-sm">Total Liquidity</span>
                <span class="float-end float-md-none pb-2">${{ $liquidityData['totalLiquidity'] ?? '' }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0 order-0">
                <span class="d-inline d-md-block text-muted fs-sm">Pooled ZNN</span>
                <span class="float-end float-md-none pb-2">{{ $liquidityData['pooledWznn'] ?? '' }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0 order-0">
                <span class="d-inline d-md-block text-muted fs-sm">Pooled ETH</span>
                <span class="float-end float-md-none pb-2">{{ $liquidityData['pooledWeth'] ?? '' }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-24 text-center">
            <a href="{{ $affiliateLink }}" target="_blank" class="btn btn-lg btn-outline-primary">
                Bridge tokens now
                <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</div>
