<header class="header navbar bg-none border-0 navbar-expand-lg mb-2">
    <div class="container">
        <div class="d-flex align-items-center">
            {!! svg('logo', 'text-primary me-3', 'width: 45px') !!}
            <div class="text-nowrap">
                <h5 class="mb-0">
                    <a href="{{ route('home') }}" class="app-name">
                        {{ config('app.name') }}
                    </a>
                </h5>
                {{ $breadcrumbs ?? '' }}
            </div>
        </div>
        <x-includes.navbar/>
    </div>
</header>
