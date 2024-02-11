<div wire:init="loadFundingData">
    <div class="bg-secondary shadow rounded-2 mb-4 p-3">
        <div class="d-block d-md-flex justify-content-md-evenly">
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">ZNN</span>
                <span class="float-end float-md-none text-zenon-green">{{ $acceleratorContract?->display_znn_balance }}</span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted fs-sm">QSR</span>
                <span class="float-end float-md-none text-zenon-blue">{{ $acceleratorContract?->display_qsr_balance }}</span>
            </div>
            <div class="text-start text-md-center">
                <span class="d-inline d-md-block text-muted fs-sm">USD</span>
                <span class="float-end float-md-none text-white opacity-80">{{ $acceleratorContract?->display_usd_balance }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-24 col-md-12">
            <div id="chart-az-funding-znn" class="mb-3 mb-md-0"></div>
        </div>
        <div class="col-24 col-md-12">
            <div id="chart-az-funding-qsr" class="mb-3 mb-md-0"></div>
        </div>
    </div>
</div>
