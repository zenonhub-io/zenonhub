<div wire:init="loadNetworkData">
    @if($networkInfo)
        <div class="list-group list-group-flush">
            @foreach($networkInfo as $network)
                <div class="list-group-item px-4 pb-4">
                    <div class="d-block d-md-flex w-100 justify-content-md-between align-items-end mb-2">
                        <h4 class="mb-1 mb-md-0">{{ $network->name }}</h4>
                        <small>
                            <a href="https://etherscan.io/address/{{ $network->contractAddress }}">
                                {{ short_hash($network->contractAddress, 12) }}
                            </a>
                        </small>
                    </div>
                    <pre class="line-numbers"><code class="lang-json">{{ pretty_json($network->tokenPairs) }}</code></pre>
                </div>
            @endforeach
        </div>
    @endif
</div>
