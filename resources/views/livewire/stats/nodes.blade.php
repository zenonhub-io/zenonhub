<div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="mb-0">Public nodes</h4>
        </div>
        @if (! $updated)
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
                    <select id="sections" class="form-control" wire:change="$emit('tabChange', $event.target.value)">
                        <option value="map" {{ $tab === 'map' ? 'selected' : '' }}>Map</option>
                        <option value="countries" {{ $tab === 'countries' ? 'selected' : '' }}>Countries</option>
                        <option value="cities" {{ $tab === 'cities' ? 'selected' : '' }}>Cities</option>
                        <option value="networks" {{ $tab === 'networks' ? 'selected' : '' }}>Networks</option>
                        <option value="versions" {{ $tab === 'versions' ? 'selected' : '' }}>Versions</option>
                    </select>
                </div>
                <div class="d-none d-md-block">
                    <ul class="nav nav-tabs-alt card-header-tabs">
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'map' ? 'active' : '' }}" wire:click="$emit('tabChange', 'map')">
                                Map
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'countries' ? 'active' : '' }}" wire:click="$emit('tabChange', 'countries')">
                                Countries
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'cities' ? 'active' : '' }}" wire:click="$emit('tabChange', 'cities')">
                                Cities
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'networks' ? 'active' : '' }}" wire:click="$emit('tabChange', 'networks')">
                                Networks
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link {{ $tab === 'versions' ? 'active' : '' }}" wire:click="$emit('tabChange', 'versions')">
                                Versions
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane show active">
                        @if ($tab === 'map')
                            <livewire:stats.nodes.map key="{{now()}}" />
                        @elseif ($tab === 'countries')
                            <livewire:stats.nodes.countries key="{{now()}}" />
                        @elseif ($tab === 'cities')
                            <livewire:stats.nodes.cities key="{{now()}}" />
                        @elseif ($tab === 'networks')
                            <livewire:stats.nodes.networks key="{{now()}}" />
                        @elseif ($tab === 'versions')
                            <livewire:stats.nodes.versions key="{{now()}}" />
                        @endif
                        <div class="text-center text-md-end mt-3">
                            <span class="fs-sm text-muted">Updated: {{ $updated }} | Data provided by <a href="https://github.com/Sol-Sanctum/Zenon-PoCs/tree/main/znn_node_info">Sol Sanctum</a></span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="{{ mix('js/pages/stats/nodes.js') }}"></script>
    @endpush
</div>
