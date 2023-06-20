<div>
    <div class="card shadow mb-4">
        <div class="card-header border-bottom">
            <span class="float-end">
                {!! $project->display_badge !!}
            </span>
            <div class="text-muted">
                Project
            </div>
            <h4 class="mb-0">
                {{ $project->name }}
            </h4>
        </div>
        <div class="card-body mb-0">
            <div class="row">
                <div class="col-24">
                    <x-az-card-header :item="$project"/>
                </div>
                <div class="col-24">
                    <ul class="list-group list-group-flush mb-0">
                        <li class="list-group-item">
                            {{ $project->description }}
                        </li>
                        <li class="list-group-item">
                            <span class="d-block fs-sm text-muted">Link</span>
                            <a href="{{ $project->url }}" target="_blank">{{ $project->url }}</a>
                        </li>
                        <li class="list-group-item">
                            <span class="d-block fs-sm text-muted">Owner</span>
                            <x-address :account="$project->owner"/>
                        </li>
                        <li class="list-group-item">
                            <span class="d-block fs-sm text-muted">Created</span>
                            {{ $project->created_at->format(config('zenon.date_format')) }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <ul class="nav nav-tabs-alt card-header-tabs">
                <li class="nav-item">
                    <button class="btn nav-link {{ $tab === 'votes' ? 'active' : '' }}" wire:click="$set('tab', 'votes')">
                        Votes
                    </button>
                </li>
                <li class="nav-item">
                    <button class="btn nav-link {{ $tab === 'json' ? 'active' : '' }}" wire:click="$set('tab', 'json')">
                        JSON
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade show active">
                @if ($tab === 'votes')
                    <livewire:tables.project-votes :project="$project" />
                @elseif ($tab === 'json')
                    <div class="p-4">
                        @if ($project->raw_json)
                            <pre class="line-numbers"><code class="lang-json">{{ pretty_json($project->raw_json) }}</code></pre>
                        @else
                            <x-alert
                                message="Unable to load JSON data"
                                type="info"
                                icon="info-circle-fill"
                                class="d-flex mb-0"
                            />
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    <livewire:az.project-phases :project="$project" />
</div>
