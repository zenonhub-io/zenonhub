<div>
    <form wire:submit="makeRequest">
        <x-honeypot livewire-model="extraFields" />

        <div class="vstack gap-6">
            <div class="row align-items-center">
                @php($uuid = Str::random(8))
                <div class="col-lg-4">
                    <x-forms.label :label="__('Request')" for="{{ $uuid }}" />
                </div>
                <div class="col-lg-12">
                    <select class="form-control" wire:change="setRequest($event.target.value)">
                        <option value="null">Choose request</option>
                        @foreach ($availableRequests as $group => $apiRequests)
                            <optgroup label="{{ $group }}">
                                @foreach ($apiRequests as $apiRequest)
                                    <option value="{{ $apiRequest['name'] }}" {{ ($request === $apiRequest['name'] ? 'selected' : '') }}>{{ $apiRequest['name'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>

            @if (! $request)
                <hr class="my-0">
                <h4>{{ __('Example requests') }}</h4>
                <div class="row align-items-center">
                    <div class="col-24">
                        <div class="list-group shadow pe-0">
                            @foreach([
                                'Latest AZ Projects' => 'request=Accelerator.getAll&data[page]=0&data[perPage]=10',
                                'Top 30 Pillars' => 'request=Pillar.getAll&data[page]=0&data[perPage]=30',
                                'Bridge Status' => 'request=Bridge.getBridgeInfo',
                                'Pending Unwraps' => 'request=Bridge.getAllUnsignedWrapTokenRequests&data[pageIndex]=0&data[pageSize]=5',
                                'Latest Momentum' => 'request=Ledger.getFrontierMomentum',
                                'Account Info' => 'request=Ledger.getAccountInfoByAddress',
                                'ZNN Token Info' => 'request=Token.getByZts&data[token]=zts1znnxxxxxxxxxxxxx9z4ulx',
                            ] as $title => $link)
                                <div class="list-group-item d-flex align-items-center">
                                    <x-link :href="url('tools/api-playground?' . $link)" class="stretched-link">
                                        {{ __($title) }}
                                    </x-link>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($inputs)
                <hr class="my-0">
                <h4>{{ __('Inputs') }}</h4>
                @foreach($inputs as $input)
                    <div class="row align-items-center">
                        @php($uuid = Str::random(8))
                        <div class="col-lg-4">
                            <x-forms.label :label="__($input['displayName'] . ' (' . $input['type'] . ')')" for="{{ $uuid }}" />
                        </div>
                        <div class="col-lg-12">
                            @if($input['type'] === 'array')
                                <x-forms.inputs.textarea id="{{ $uuid }}" :name="$input['name']"
                                                      :value="$input['default']"
                                                      wire:model="data.{{ $input['name'] }}"
                                />
                                <p class="text-muted text-sm">Separate each array item with a new line</p>
                            @else
                                <x-forms.inputs.input id="{{ $uuid }}" :name="$input['name']"
                                                      :value="$input['default']"
                                                      :placeholder="$input['default']"
                                                      wire:model="data.{{ $input['name'] }}"
                                />
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif

            @if ($request)
                <div class="row align-items-center">
                    <div class="col-lg-16">
                        <button type="submit" class="btn w-100 btn-outline-primary" wire:loading.attr="disabled">
                            <i class="bi bi-cloud-fill me-2"></i>
                            Make request
                        </button>
                    </div>
                </div>
            @endif

            <div class="row align-items-center" wire:loading>
                <div class="col-lg-16">
                    <x-alerts.alert type="info" class="mb-6">
                        <i class="bi bi-arrow-repeat spin me-2"></i> {{ __('Processing request...') }}
                    </x-alerts.alert>
                </div>
            </div>

            @if ($result)
                <div wire:loading.remove>
                    <hr class="mt-0 mb-6">
                    <h4 class="mb-4">{{ __('Results') }}</h4>
                    <div class="row align-items-center">
                        @php($uuid = Str::random(8))
                        <div class="col-lg-24">
                            <x-forms.inputs.group>
                                <span class="input-group-text">
                                    {{ Str::upper($method) }}
                                </span>
                                <x-forms.inputs.input id="{{ $uuid }}" name="url" wire:model="url "/>
                                <span class="input-group-text js-copy" data-clipboard-target="#{{ $uuid }}" data-bs-toggle="tooltip" data-bs-title="Copy">
                                    <i class="bi-clipboard text-zenon-blue"></i>
                                </span>
                            </x-forms.inputs.group>
                        </div>
                    </div>

                    <div class="row align-items-center mt-6">
                        <div class="col-md-24">
                            <x-code-highlighters.json :code="$result" />
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </form>
</div>
