<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <div class="container">
        <div class="row">
            <div class="col-24 col-md-16 offset-md-4 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header text-center">
                        <h4 class="mb-0">Public Nodes</h4>
                    </div>
                    <div class="card-body text-center">
                        <p>We provide public nodes available for anyone to use. These nodes serve secure connections to the Zenon Network are load balances with servers located in Europe and the US.</p>
                    </div>
                    <div class="card-body text-center px-1 pb-4 pt-0">
                        <span class="text-primary text-break fs-5 d-block mb-4 user-select-all">
                            {{ config('zenon.public_node_https') }} <i class="bi bi-clipboard ms-1 hover-text js-copy" data-clipboard-text="{{ config('zenon.public_node_https') }}" data-bs-toggle="tooltip" data-bs-title="Copy"></i>
                        </span>
                        <span class="text-primary text-break fs-5 d-block user-select-all">
                            {{ config('zenon.public_node_wss') }}  <i class="bi bi-clipboard ms-1 hover-text js-copy" data-clipboard-text="{{ config('zenon.public_node_wss') }}" data-bs-toggle="tooltip" data-bs-title="Copy"></i>
                        </span>
                    </div>
                </div>
                <livewire:site.public-nodes/>
            </div>
        </div>
    </div>
</x-layouts.app>
