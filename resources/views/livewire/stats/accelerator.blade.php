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
                        <div class="bg-secondary shadow rounded-2 mb-4 p-3">
                            <div class="d-block d-md-flex justify-content-md-evenly">
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block text-muted">ZNN</span>
                                    <span class="float-end float-md-none text-zenon-green">{{ $acceleratorContract->display_znn_balance }}</span>
                                </div>
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block text-muted">QSR</span>
                                    <span class="float-end float-md-none text-zenon-blue">{{ $acceleratorContract->display_qsr_balance }}</span>
                                </div>
                                <div class="text-start text-md-center">
                                    <span class="d-inline d-md-block text-muted">USD</span>
                                    <span class="float-end float-md-none text-white opacity-80">{{ $acceleratorContract->display_usd_balance }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-24 col-md-12">
                                <div id="chart-az-funding-znn" class="mb-3 mb-md-0"></div>
                            </div>
                            <div class="col-24 col-md-12">
                                <div id="chart-az-funding-qsr" class="mb-3 mb-md-0"></div>
                            </div>
                        </div>
                    @elseif ($tab === 'projects')
                        <div class="bg-secondary shadow rounded-2 mb-4 p-3">
                            <div class="d-block d-md-flex justify-content-md-evenly">
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block text-muted">New</span>
                                    <span class="float-end float-md-none text-white opacity-80">{{ $azProjectTotals['data'][0] }}</span>
                                </div>
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block text-muted">Accepted</span>
                                    <span class="float-end float-md-none text-primary">{{$azProjectTotals['data'][1] }}</span>
                                </div>
                                <div class="text-start text-md-center mb-2 mb-md-0">
                                    <span class="d-inline d-md-block text-muted">Completed</span>
                                    <span class="float-end float-md-none text-success">{{ $azProjectTotals['data'][2] }}</span>
                                </div>
                                <div class="text-start text-md-center">
                                    <span class="d-inline d-md-block text-muted">Rejected</span>
                                    <span class="float-end float-md-none text-danger">{{ $azProjectTotals['data'][3] }}</span>
                                </div>
                            </div>
                        </div>
                        <div id="chart-az-project-totals" class="mb-md-0"></div>
                    @elseif ($tab === 'engagement')
                        <div class="table-responsive p-n3">
                            <table class="table table-nowrap align-middle table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>
                                        <button type="button" class="btn btn-sort" wire:click="engagementSortBy('name')">
                                            <x-table-sort-button :sort="$engagementSort" :order="$engagementOrder" check="name" title="Pillar"/>
                                        </button>
                                    </th>
                                    <th>
                                        <button type="button" class="btn btn-sort" wire:click="engagementSortBy('az_engagement')">
                                            <x-table-sort-button :sort="$engagementSort" :order="$engagementOrder" check="az_engagement" title="Engagement" tooltip="% of Accelerator projects and phases voted on"/>
                                        </button>
                                    </th>
                                    <th>
                                        <button type="button" class="btn btn-sort" wire:click="engagementSortBy('az_avg_vote_time')">
                                            <x-table-sort-button :sort="$engagementSort" :order="$engagementOrder" check="az_avg_vote_time" title="Avg vote time"/>
                                        </button>
                                    </th>
                                    <th>
                                        Votes
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($engagementData as $pillar)
                                    <tr>
                                        <td>
                                            <a href="{{ route('pillars.detail', ['slug' => $pillar->slug]) }}">
                                                {{ $pillar->name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ number_format($pillar->az_engagement) }}%
                                        </td>
                                        <td>
                                            {{ $pillar->display_az_avg_vote_time }}<br>
                                        </td>
                                        <td>
                                            {{ number_format($pillar->az_votes()->count()) }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="m-4 mt-2">
                            {{ $engagementData->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ mix('js/pages/stats/accelerator.js') }}"></script>
        <script>
            ((ZenonHub) => {

                ZenonHub.addData('azFundingZnn', @json($azFundingZnn['data']));
                ZenonHub.addData('azFundingZnnLabels', @json($azFundingZnn['labels']));

                ZenonHub.addData('azFundingQsr', @json($azFundingQsr['data']));
                ZenonHub.addData('azFundingQsrLabels', @json($azFundingQsr['labels']));

                ZenonHub.addData('azProjectTotals', @json($azProjectTotals['data']));
                ZenonHub.addData('azProjectTotalLabels', @json($azProjectTotals['labels']));

                ZenonHub.addData('initialTab', '{{ $tab }}');
            })(window.zenonHub);
        </script>
    @endpush
</div>
