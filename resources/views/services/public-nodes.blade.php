<x-app-layout>
    <x-includes.header :title="__('Public Nodes')" />

    <div class="container-fluid px-3 px-md-6 mb-6">
        <p>
            Our public nodes are freely accessible to everyone, offering secure and fast connections to the Zenon Network with load-balanced servers located in Europe and the US.
        </p>
        <hr>
        <x-cards.card>
            <x-cards.body class="text-center lead">
                <div class="text-primary text-break mb-4 user-select-all">
                    {{ config('zenon-hub.public_node_https') }} <i class="bi bi-copy ms-1 text-muted js-copy" data-clipboard-text="{{ config('zenon.public_node_https') }}" data-bs-toggle="tooltip" data-bs-title="Copy"></i>
                </div>
                <hr>
                <div class="text-primary text-break user-select-all">
                    {{ config('zenon-hub.public_node_wss') }}  <i class="bi bi-copy ms-1 text-muted js-copy" data-clipboard-text="{{ config('zenon.public_node_wss') }}" data-bs-toggle="tooltip" data-bs-title="Copy"></i>
                </div>
            </x-cards.body>
        </x-cards.card>
    </div>

    <livewire:services.public-nodes />

</x-app-layout>
