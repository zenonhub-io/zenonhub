<x-app-layout>
    <x-includes.header :title="__('Public Nodes')" />

    <div class="container-fluid px-3 px-md-6 mb-4">
        <p>
            Our nodes are freely available for anyone to use. They provide secure connections to the Zenon Network and are load balanced with servers in Europe and the US.
        </p>

        <hr>

        <div class="bg-dark-subtle p-4 rounded-2 border shadow-inset lead">
            <div class="text-primary text-break mb-4 user-select-all">
                {{ config('zenon-hub.public_node_https') }} <i class="bi bi-copy ms-1 text-muted js-copy" data-clipboard-text="{{ config('zenon.public_node_https') }}" data-bs-toggle="tooltip" data-bs-title="Copy"></i>
            </div>
            <div class="text-primary text-break user-select-all">
                {{ config('zenon-hub.public_node_wss') }}  <i class="bi bi-copy ms-1 text-muted js-copy" data-clipboard-text="{{ config('zenon.public_node_wss') }}" data-bs-toggle="tooltip" data-bs-title="Copy"></i>
            </div>
        </div>
    </div>

    <livewire:services.public-nodes />

</x-app-layout>
