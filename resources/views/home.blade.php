<x-app-layout>
    <x-includes.header :title="__('Explore the Network of Momentum')" :responsive-border="false" />

    <div class="container-fluid px-3 px-md-6">
        <div class="row mb-6 gy-6">
            <div class="col-24 col-sm-12 col-xl-6">
                <livewire:tiles.znn-supply lazy />
            </div>
            <div class="col-24 col-sm-12 col-xl-6">
                <livewire:tiles.qsr-supply lazy />
            </div>
            <div class="col-24 col-sm-12 col-xl-6">
                <livewire:tiles.accounts-overview lazy />
            </div>
            <div class="col-24 col-sm-12 col-xl-6">
                <livewire:tiles.transactions-overview lazy />
            </div>
        </div>

        <div class="row mb-6 gy-6">
            <div class="col-24 col-lg-12">
                <livewire:tiles.transactions-daily lazy timeframe="30d" />
            </div>
            <div class="col-24 col-lg-12">
                <livewire:tiles.accounts-total lazy timeframe="30d" />
            </div>
        </div>

        <div class="row mb-6 gy-6">
            <div class="col-24 col-md-12 col-xl-8">
                <livewire:tiles.pillars-top lazy />
            </div>
            <div class="col-24 col-md-12 col-xl-8">
                <livewire:tiles.projects-latest lazy />
            </div>
            <div class="col-24 col-xl-8">
                <x-cards.card>
                    <x-cards.heading class="d-flex align-items-center">
                        <i class="bi bi-person-circle text-lg me-3"></i>
                        <h4 class="flex-grow-1 mb-0">
                            {{ __('Members') }}
                        </h4>
                    </x-cards.heading>
                    <x-cards.body>
                        <p class="mb-0 lead">
                            {{ __('Join :site_name for free today!', [
                                'site_name' => config('app.name')
                            ]) }}
                        </p>
                        <x-link :href="route('register')" class="btn btn-outline-primary btn-sm w-100 mt-3">
                            {{ __('Sign up') }}
                            <i class="bi bi-arrow-right ms-2"></i>
                        </x-link>
                    </x-cards.body>
                    <ul class="list-group list-group-flush mb-0">
                        <li class="list-group-item px-6">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <i class="bi bi-bell-fill me-2 text-lg"></i>
                                <h5 class="me-auto">{{ __('Notifications') }}</h5>
                            </div>
                            <p class="text-sm">{{ __('Receive alerts for on-chain events including AZ and Pillar changes') }}</p>
                        </li>
                        <li class="list-group-item px-6">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <i class="bi bi-star-fill me-2 text-lg"></i>
                                <h5 class="me-auto">{{ __('Favorites') }}</h5>
                            </div>
                            <p class="text-sm">{{ __('Save a list of your favorite addresses with nicknames and notes') }}</p>
                        </li>
                        <li class="list-group-item px-6">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <i class="bi bi-key-fill me-2 text-lg"></i>
                                <h5 class="me-auto">{{ __('API Keys') }}</h5>
                            </div>
                            <p class="text-sm">{{ __('Generate and manage access keys for using our API and services') }}</p>
                        </li>
                    </ul>
                </x-cards.card>
            </div>
        </div>
    </div>

</x-app-layout>
