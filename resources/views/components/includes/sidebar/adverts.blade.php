<div id="ads-carousel" class="carousel slide" data-bs-ride="carousel" data-bs-theme="dark" data-bs-interval="10000">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <x-cards.card class="mx-4">
                <x-cards.body class="pt-3">
                    <div class="vstack text-center">
                        <i class="bi bi-badge-ad-fill text-white text-2xl"></i>
                        <p class="lead mb-4">
                            {{ __('Advertise your brand here!') }}
                        </p>
                        <x-link :href="route('advertise')" class="btn btn-sm btn-outline-primary w-full">
                            {{ __('Lets go!') }} <i class="bi bi-arrow-right ms-2"></i>
                        </x-link>
                    </div>
                </x-cards.body>
            </x-cards.card>
        </div>
        {{--        <div class="carousel-item active">--}}
        {{--            <x-cards.card class="mx-4">--}}
        {{--                <x-cards.body class="pt-3">--}}
        {{--                    <div class="vstack text-center">--}}
        {{--                        <i class="bi bi-badge-ad-fill text-white text-2xl"></i>--}}
        {{--                        <p class="lead mb-4">--}}
        {{--                            {{ __('HyperQube pillar signup open') }}--}}
        {{--                        </p>--}}
        {{--                        <x-link :href="route('advertise')" class="btn btn-sm btn-outline-primary w-full">--}}
        {{--                            {{ __('Join now!') }} <i class="bi bi-arrow-right ms-2"></i>--}}
        {{--                        </x-link>--}}
        {{--                    </div>--}}
        {{--                </x-cards.body>--}}
        {{--            </x-cards.card>--}}
        {{--        </div>--}}
    </div>
</div>


