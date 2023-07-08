<div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="mb-0">Accelerator Z</h4>
        </div>
        <div class="card-header">
            <div class="d-md-none">
                <select id="sections" class="form-control" wire:change="$emit('tabChange', $event.target.value)">
                    <option value="funding" {{ $tab === 'funding' ? 'selected' : '' }}>Funding</option>
                    <option value="projects" {{ $tab === 'projects' ? 'selected' : '' }}>Projects</option>
                    <option value="engagement" {{ $tab === 'engagement' ? 'selected' : '' }}>Engagement</option>
                </select>
            </div>
            <div class="d-none d-md-block">
                <ul class="nav nav-tabs-alt card-header-tabs">
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'funding' ? 'active' : '' }}" wire:click="$emit('tabChange', 'funding')">
                            Funding
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'projects' ? 'active' : '' }}" wire:click="$emit('tabChange', 'projects')">
                            Projects
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn nav-link {{ $tab === 'engagement' ? 'active' : '' }}" wire:click="$emit('tabChange', 'engagement')">
                            Engagement
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body {{ ($tab === 'engagement' ? 'px-0 pb-0' : '') }}">
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
    @push('scripts')
        <script src="{{ mix('js/pages/stats/accelerator.js') }}"></script>
        <script>
            ((ZenonHub) => {
                ZenonHub.addData('initialTab', '{{ $tab }}');
            })(window.zenonHub);
        </script>
    @endpush
</div>
