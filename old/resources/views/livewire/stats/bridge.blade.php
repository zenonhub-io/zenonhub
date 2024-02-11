<div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="mb-0">Multichain Bridge</h4>
        </div>
        <div class="card-header">
            <livewire:utilities.tab-header activeTab="{{ $tab }}" :tabs="[
                'overview' => 'Overview',
                'security' => 'Security',
                'actions' => 'Actions',
                'orchestrators' => 'Orchestrators',
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
            <div class="card-body {{ (in_array($tab, ['actions', 'orchestrators']) ? 'p-0' : '') }}">
                <div class="tab-content">
                    <div class="tab-pane show active">
                        @if ($tab === 'overview')
                            <livewire:stats.bridge.overview key="{{now()}}" />
                        @elseif ($tab === 'security')
                            <livewire:stats.bridge.security key="{{now()}}" />
                        @elseif ($tab === 'actions')
                            <livewire:stats.bridge.actions key="{{now()}}" />
                        @elseif ($tab === 'orchestrators')
                            <livewire:stats.bridge.orchestrators key="{{now()}}" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
