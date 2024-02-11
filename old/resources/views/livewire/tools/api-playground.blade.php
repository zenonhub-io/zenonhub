<div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="mb-0">API Playground</h4>
        </div>
        <div class="card-body">
            <div class="mb-0">
                Use the form to query the Network of Momentum. We provide http endpoints to all the request listed below, test your requests here and use them in your own project. Or download our <a href="https://github.com/zenonhub-io/znn-php">PHP SDK</a>.
            </div>
            <hr class="border-secondary my-4">
            <form action="#" method="post" class="needs-validation">
                @csrf
                <div class="mb-4">
                    <h5>Request</h5>
                    <select id="form-request" class="form-select" wire:change="setRequest($event.target.value)">
                        <option value="null">Choose request</option>
                        @foreach ($requests as $group => $links)
                            <optgroup label="{{ $group }}">
                                @foreach ($links as $link)
                                    <option value="{{ $link['name'] }}" {{ ($request === $link['name'] ? 'selected' : '') }}>{{ $link['name'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                @if ($request)
                    @if (! empty($inputs))
                        <div class="mb-4">
                            <h5>Inputs</h5>
                            @php ($i = 0)
                            @foreach ($inputs as $name => $type)
                                @php ($default = null)
                                @php ($i++)
                                @if (str_contains($type, ':'))
                                    @php (list($type, $default) = explode(':', $type))
                                @endif
                                <div class="{{ ($i !== count($inputs) ? 'mb-3' : '') }}">
                                    <label class="form-label" for="form-input-{{ $name }}">{{ ucfirst(Str::snake($name, ' ')) }} ({{ $type }})</label>
                                    <input
                                        type="text"
                                        id="form-input-{{ $name }}"
                                        name="{{ $name }}"
                                        class="form-control"
                                        placeholder="{{ (string) $default }}"
                                        wire:model.defer="data.{{ $name }}"
                                    >
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <button type="button" class="btn w-100 btn-outline-primary" wire:click="makeRequest">
                        <i class="bi bi-cloud-fill me-2"></i>
                        Make request
                    </button>
                @endif
                <div class="w-100 mt-4" wire:loading>
                    <x-alert
                        message="Processing request..."
                        type="info"
                        icon="arrow-repeat spin"
                        class="d-flex justify-content-center mb-0"
                    />
                </div>
            </form>
            @if ($result)
                <div wire:loading.remove>
                    <hr class="border-bottom border-1 border-secondary mt-4 mb-4">
                    <h5>Results</h5>
                    <div class="mb-3">
                        <label class="form-label" for="form-url">URL</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                {{ $method }}
                            </span>
                            <input
                                type="text"
                                id="form-url"
                                class="form-control"
                                value="{{ $url }}"
                                readonly
                                wire:model.defer="url"
                            >
                            <span class="input-group-text js-copy" data-clipboard-target="#form-url" data-bs-toggle="tooltip" data-bs-title="Copy">
                                <i class="bi-clipboard text-zenon-blue"></i>
                            </span>
                        </div>
                    </div>
                    <label class="form-label" for="form-result">Response</label>
                    <pre class="line-numbers"><code class="lang-json">{{ $result }}</code></pre>
                </div>
            @endif
        </div>
    </div>
</div>
