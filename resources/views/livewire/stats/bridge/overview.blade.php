<div wire:init="loadLiquidityData">
    <div class="bg-secondary shadow rounded-2 p-3 mb-4">
        <div class="d-block d-md-flex justify-content-md-evenly">
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted">Status</span>
                <span class="float-end float-md-none">
                    <span class="legend-indicator bg-{{ (! $halted ? 'success' : 'danger') }}"></span>
                </span>
            </div>
            <div class="text-start text-md-center mb-2 mb-md-0">
                <span class="d-inline d-md-block text-muted">Admin</span>
                <span class="float-end float-md-none">
                    <x-address :account="$adminAddress" :eitherSide="10" breakpoint="lg"/>
                </span>
            </div>
        </div>
    </div>

    Liquidity
    <pre class="line-numbers mb-4"><code class="lang-json">{{ pretty_json($liquidityData) }}</code></pre>

    Holders
    <pre class="line-numbers"><code class="lang-json">{{ pretty_json($holders) }}</code></pre>
</div>
