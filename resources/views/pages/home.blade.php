<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <div class="container">
        <div class="row">
            @foreach($data['stats'] as $stat => $value)
                <div class="col-8 col-lg-4 mb-1">
                    <div class="p-1 bg-secondary shadow rounded-2 text-center mb-3">
                        <span class="d-block fs-sm text-muted">{{ Str::ucfirst($stat) }}</span>
                        {{ $value }}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-24 col-md-14 col-lg-16">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-24 col-lg-16">
                                <h5 class="card-title">
                                    <i class="bi bi-search me-2"></i>
                                    Explore the network
                                </h5>
                                Search and explore the Zenon Network. View the latest <span class="fw-bold">momentums</span>, <span class="fw-bold">transaction</span>, <span class="fw-bold">accounts</span>, <strong>tokens</strong> and more.
                            </div>
                            <div class="col-24 col-lg-8 my-auto">
                                <a class="btn btn-lg btn-outline-primary w-100 mt-3 mt-lg-0" href="{{ route('explorer.overview') }}">
                                    <i class="bi bi-search me-2"></i>
                                    Explore
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-24 col-lg-16">
                                <h5 class="card-title">
                                    <i class="bi bi-person-fill me-2"></i>
                                    @if (auth()->check())
                                        Manage your account
                                    @else
                                        Stay up to date
                                    @endif
                                </h5>
                                @if (auth()->check())
                                    Edit your account details, control <span class="fw-bold">notification subscriptions</span> and change your password.
                                @else
                                    Sign-up to receive notifications for on-chain activity including <span class="fw-bold">AZ updates</span>, <span class="fw-bold">reward</span> and <span class="fw-bold">pillar</span> changes.
                                @endif
                            </div>
                            <div class="col-24 col-lg-8 my-auto">
                                @if (auth()->check())
                                    <a class="btn btn-lg btn-outline-primary w-100 mt-3 mt-lg-0" href="{{ route('account.details') }}">
                                        <i class="bi bi-person-circle me-2"></i>
                                        Manage
                                    </a>
                                @else
                                    <a class="btn btn-lg btn-outline-primary w-100 mt-3 mt-lg-0" href="{{ route('sign-up') }}">
                                        <i class="bi bi-person-plus-fill me-2"></i>
                                        Join
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-24 col-sm-12 mb-4">
                        <div class="card card-hover h-100 shadow text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
                                    <a href="{{ route('tools.plasma-bot') }}" class="stretched-link">
                                        <span class="d-block mb-2">
                                            {!! svg('tools/plasma-bot', 'mb-2', 'height: 2.3rem') !!}
                                        </span>
                                        <h5>Plasma Bot</h5>
                                    </a>
                                    <p class="mb-0">
                                        Generate plasma for an address
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-24 col-sm-12 mb-4">
                        <div class="card card-hover h-100 shadow text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
                                    <a href="{{ route('tools.api-playground') }}" class="stretched-link">
                                        <span class="d-block mb-2">
                                            {!! svg('tools/api-playground', 'mb-2', 'height: 2.3rem') !!}
                                        </span>
                                        <h5>API Playground</h5>
                                    </a>
                                    <p class="mb-0">
                                        Explore the networks RPC endpoints
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-24 col-sm-12 mb-4">
                        <div class="card card-hover h-100 shadow text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
                                    <a href="{{ route('stats.nodes') }}" class="stretched-link">
                                        <span class="d-block mb-2">
                                            {!! svg('stats/nodes', 'mb-2', 'height: 2.3rem') !!}
                                        </span>
                                        <h5>Public Nodes</h5>
                                    </a>
                                    <p class="mb-0">
                                        View the networks public node stats
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-24 col-sm-12 mb-4">
                        <div class="card card-hover h-100 shadow text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
                                    <a href="{{ route('stats.accelerator') }}" class="stretched-link">
                                        <span class="d-block mb-2">
                                            {!! svg('stats/accelerator', 'mb-2', 'height: 2.3rem') !!}
                                        </span>
                                        <h5>Accelerator Z</h5>
                                    </a>
                                    <p class="mb-0">
                                        View the Accelerator Z contract stats
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-24 col-md-10 col-lg-8 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title border-bottom border-light pb-3">
                            {!! svg('az', 'me-2', 'height: 18px') !!} Latest projects
                        </h5>
                        <ul class="list-group list-group-flush mb-0">
                            @foreach ($data['accelerator'] as $project)
                                <li class="list-group-item d-flex align-items-start justify-content-between">
                                    <div class="d-block">
                                        <a class="fw-bold" href="{{ route('az.project', ['hash' => $project->hash]) }}">
                                            {{ $project->name }}
                                        </a>
                                        <span class="fs-sm d-block text-muted">{{ $project->phases()->count() }} {{ Str::plural('Phase', $project->phases()->count()) }}</span>
                                    </div>
                                    <div class="d-block text-end">
                                        <span class="fs-sm d-block text-muted">Status</span>
                                        {!! $project->display_status !!}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
