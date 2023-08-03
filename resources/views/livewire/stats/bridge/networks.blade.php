<div wire:init="loadNetworkData">
    @if($networkInfo)
        <div class="list-group list-group-flush">
            @foreach($networkInfo as $network)
                <div class="list-group-item px-4 pb-4">
                    <div class="d-flex w-100 justify-content-between align-items-baseline">
                        <h4 class="mb-2">{{ $network->name }}</h4>
                        <small>
                            <a href="https://etherscan.io/address/{{ $network->contractAddress }}">
                                {{ $network->contractAddress }}
                            </a>
                        </small>
                    </div>
                    <pre class="line-numbers"><code class="lang-json">{{ pretty_json($network->tokenPairs) }}</code></pre>
                </div>
            @endforeach
        </div>
    @endif
</div>
