<div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="mb-0">Node statistics</h4>
        </div>
        @if (! $dataCached)
            <div class="card-body">
                <x-alert
                    message="Unable to load node stats, please try again..."
                    type="info"
                    icon="info-circle-fill"
                    class="d-flex justify-content-center mb-0"
                />
            </div>
        @else
            <div class="card-header">
                <div class="d-md-none">
                    <select id="api-sections" class="form-control" wire:change="$emit('tabChange', $event.target.value)">
                        <option value="map" {{ $tab === 'map' ? 'selected' : '' }}>Map</option>
                        <option value="countries" {{ $tab === 'countries' ? 'selected' : '' }}>Countries</option>
                        <option value="cities" {{ $tab === 'cities' ? 'selected' : '' }}>Cities</option>
                        <option value="networks" {{ $tab === 'networks' ? 'selected' : '' }}>Networks</option>
                    </select>
                </div>
                <div class="d-none d-md-block">
                    <ul class="nav nav-tabs-alt card-header-tabs">
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'map' ? 'active' : '' }}" wire:click="$emit('tabChange', 'map')">
                                <i class="bi bi-globe opacity-70 me-2"></i> Map
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'countries' ? 'active' : '' }}" wire:click="$emit('tabChange', 'countries')">
                                <i class="bi bi-geo-alt-fill opacity-70 me-2"></i> Countries
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'cities' ? 'active' : '' }}" wire:click="$emit('tabChange', 'cities')">
                                <i class="bi bi-building-fill opacity-70 me-2"></i> Cities
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'networks' ? 'active' : '' }}" wire:click="$emit('tabChange', 'networks')">
                                <i class="bi bi-hdd-network-fill opacity-70 me-2"></i> Networks
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane show active">
                        @if ($tab === 'map')
                            <p class="text-white-70">
                                Visualise the public node distribution, found <strong>{{ count($mapData['ips']) }} {{ Str::plural('node', count($mapData['ips'])) }}</strong> in <strong>{{ count($mapData['cities']) }} {{ Str::plural('city', count($mapData['cities'])) }}</strong> from <strong>{{ count($mapData['countries']) }} {{ Str::plural('country', count($mapData['countries'])) }}</strong>.
                            </p>
                            <hr class="border-secondary my-4">
                            <div class="text-center">
                                <canvas
                                    id="cobe"
                                    style="width:min(80vmin, 800px);height:min(80vmin, 800px);"
                                ></canvas>
                            </div>
                        @elseif ($tab === 'countries')
                            <div id="chart-node-countries" class="mb-3"></div>
                        @elseif ($tab === 'cities')
                            <div id="chart-node-cities" class="mb-3"></div>
                        @elseif ($tab === 'networks')
                            <div id="chart-node-networks" class="mb-3"></div>
                        @endif
                        <div class="text-center text-md-end">
                            <span class="fs-sm text-muted">Last updated: {{ $updated }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if ($dataCached)
        @push('scripts')
            <script src="{{ mix('js/pages/node-statistics.js') }}"></script>
            <script>
                ((ZenonHub) => {
                    ZenonHub.addData('nodeMapCanvasId', 'cobe');
                    ZenonHub.addData('nodeMapMarkers', [@foreach ($mapData['ips'] as $marker){ location: [{{$marker['lat']}}, {{$marker['lng']}}], size: 0.05 },@endforeach]);

                    ZenonHub.addData('nodeCountriesSeries', @json($countriesData['data']));
                    ZenonHub.addData('nodeCountriesLabels', @json($countriesData['labels']));

                    ZenonHub.addData('nodeCitiesSeries', @json($citiesData['data']));
                    ZenonHub.addData('nodeCitiesLabels', @json($citiesData['labels']));

                    ZenonHub.addData('nodeNetworkSeries', @json($networksData['data']));
                    ZenonHub.addData('nodeNetworkLabels', @json($networksData['labels']));

                    ZenonHub.addData('initialTab', '{{ $tab }}');
                })(window.zenonHub);
            </script>
        @endpush
    @endif
</div>
