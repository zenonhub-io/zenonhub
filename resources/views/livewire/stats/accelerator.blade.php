<div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="mb-0">Accelerator Z</h4>
        </div>
        <div class="card-header">
            <livewire:utilities.tab-header activeTab="{{ $tab }}" :tabs="[
                'funding' => 'Funding',
                'projects' => 'Projects',
                'engagement' => 'Engagement',
                'contributors' => 'Contributors',
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
            <div class="card-body {{ (in_array($tab, ['engagement', 'contributors']) ? 'px-0 pb-0' : '') }}">
                <div class="tab-content">
                    <div class="tab-pane show active">
                        @if ($tab === 'funding')
                            <livewire:stats.accelerator.funding key="{{now()}}" />
                        @elseif ($tab === 'projects')
                            <livewire:stats.accelerator.projects key="{{now()}}" />
                        @elseif ($tab === 'engagement')
                            <livewire:stats.accelerator.engagement key="{{now()}}" />
                        @elseif ($tab === 'contributors')
                            <livewire:stats.accelerator.contributors key="{{now()}}" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ mix('js/pages/stats/accelerator.js') }}"></script>
    @endpush
</div>
