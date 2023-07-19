<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <div class="container">
        <div class="row">
            <div class="col-24 col-md-16 offset-md-4 mb-4">
                <div class="my-0 my-md-4"></div>
                <div class="card shadow text-center">
                    <div class="card-header">
                        <h4 class="mb-0">Public Nodes</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-block">
                            <p class="mb-4">Our public nodes are available for anyone to use:</p>
                            <span class="text-primary text-break fs-5 d-block mb-4 user-select-all">
                                {{ config('zenon.public_node_https') }} <i class="bi bi-clipboard ms-2 hover-text js-copy" data-clipboard-text="{{ config('zenon.public_node_https') }}" data-bs-toggle="tooltip" data-bs-title="Copy"></i>
                            </span>
                            <span class="text-primary text-break fs-5 d-block user-select-all">
                                {{ config('zenon.public_node_wss') }}  <i class="bi bi-clipboard ms-2 hover-text js-copy" data-clipboard-text="{{ config('zenon.public_node_wss') }}" data-bs-toggle="tooltip" data-bs-title="Copy"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
