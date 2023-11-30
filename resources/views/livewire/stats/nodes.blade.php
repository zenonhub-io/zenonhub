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
                <livewire:utilities.tab-header activeTab="{{ $tab }}" :tabs="[
                    'map' => 'Map',
                    'countries' => 'Countries',
                    'cities' => 'Cities',
                    'networks' => 'Networks',
                    'versions' => 'Versions',
                ]" />
            </div>
            <div class="w-100 p-3" wire:loading>
                <x-alert
                    message="Processing request..."
                    type="info"
                    icon="arrow-repeat spin"
                    class="d-flex justify-content-center mb-0"
                />
            </div>
            <div wire:loading.remove>
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
                                <span class="fs-sm text-muted">Updated: {{ $updated }} | Data provided by <a href="https://github.com/sol-znn/znn-node-parser">Sol Sanctum</a></span>
                            </div>
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
