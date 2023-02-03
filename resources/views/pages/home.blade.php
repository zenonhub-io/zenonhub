<x-layouts.app>
    <x-slot name="pageTitle">
        {{ $meta['title'] }}
    </x-slot>
    <x-slot name="pageMetaTags">
        <meta name="description" content="{{ $meta['description'] }}">
        <meta name="og:description" content="{{ $meta['description'] }}">
    </x-slot>

    <div class="container">
        <div class="row">
            @foreach($data['stats'] as $stat => $value)
                <div class="col-12 col-md-8 col-lg-4 mb-4">
                    <div class="p-1 bg-faded-light rounded-2 text-center">
                        <span class="d-block fs-xs">{{ Str::ucfirst($stat) }}</span>
                        <span class="fw-bold fs-sm">
                            {{ $value }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-24 col-md-14 col-lg-16">
                <div class="card shadow card-hover mb-4">
                    <div class="card-body">
                        <div class="d-block d-lg-flex align-items-center">
                            <div class="p-2 flex-grow-1">
                                <h5 class="card-title">
                                    <i class="bi bi-search me-2"></i>
                                    Explore the network
                                </h5>
                                Search and explore the Network of Momentum. View the latest <span class="fw-bold">momentums</span>, <span class="fw-bold">transaction</span>, <span class="fw-bold">accounts</span>, <strong>tokens</strong> and more.
                            </div>
                            <div class="p-2 text-center">
                                <a class="btn btn-lg btn-outline-primary" href="{{ route('explorer.overview') }}">
                                    <i class="bi bi-search me-2"></i>
                                    Explore
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow card-hover mb-4">
                    <div class="card-body">
                        <div class="d-block d-lg-flex align-items-center">
                            <div class="p-2 flex-grow-1">
                                <h5 class="card-title">
                                    <i class="bi bi-bell-fill me-2"></i>
                                    Stay up to date
                                </h5>
                                Create an account to receive email notifications for on-chain activity including <span class="fw-bold">AZ updates</span>, <span class="fw-bold">reward changes</span> and <span class="fw-bold">delegator info</span> for pillar owners.
                            </div>
                            <div class="p-2 text-center">
                                <a class="btn btn-lg btn-outline-primary" href="{{ route('sign-up') }}">
                                    <i class="bi bi-person-plus-fill me-2"></i>
                                    Join
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-24 col-md-12 col-lg-8 mb-4">
                        <div class="card h-100 shadow card-hover text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
                                    <a href="{{ route('tools.api-playground') }}">
                                        <span class="d-block">
                                            <i class="bi-cloud-fill opacity-70" style="font-size:1.8rem;"></i>
                                        </span>
                                        <h5>Build</h5>
                                    </a>
                                    <p class="mb-0">
                                        Use our API in your own application.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-24 col-md-12 col-lg-8 mb-4">
                        <div class="card h-100 shadow card-hover text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
                                    <a href="{{ route('tools.broadcast-message') }}">
                                        <span class="d-block">
                                            <i class="bi-send-fill opacity-70" style="font-size:1.8rem;"></i>
                                        </span>
                                        <h5>Broadcast</h5>
                                    </a>
                                    <p class="mb-0">
                                        Send a signed message to the forum.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-24 col-lg-8 mb-4">
                        <div class="card h-100 shadow card-hover text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
                                    <a href="{{ route('tools.verify-signature') }}">
                                        <span class="d-block">
                                            <i class="bi-check2-circle opacity-70" style="font-size:1.8rem;"></i>
                                        </span>
                                        <h5>Validate</h5>
                                    </a>
                                    <p class="mb-0">
                                        Confirm a message and signature match.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-24 col-md-10 col-lg-8">
                <div class="card shadow mb-4">
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
                                        <span class="fs-sm d-block">{{ $project->phases()->count() }} {{ Str::plural('Phase', $project->phases()->count()) }}</span>
                                    </div>
                                    <div class="d-block text-end">
                                        <span class="fs-sm d-block">Status</span>
                                        <span class="fw-bold">
                                            {!! $project->display_status !!}
                                        </span>
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
