<div class="row g-0 mx-3 mt-3 mb-2 text-center">
    <div class="col">
        <x-link :href="route('info')" data-bs-toggle="tooltip" data-bs-title="{{ __('Info') }}"
                class="btn rounded-pill bg-body-secondary-hover">
            <i class="bi-info-circle-fill" style="font-size: 1.1rem;"></i>
        </x-link>
    </div>
    <div class="col">
        <x-link :href="route('donate')" data-bs-toggle="tooltip" data-bs-title="{{ __('Donate') }}"
                class="btn rounded-pill bg-body-secondary-hover">
            <i class="bi-heart-fill" style="font-size: 1.1rem;"></i>
        </x-link>
    </div>
    <div class="col">
        <x-link :href="route('services.public-nodes')" data-bs-toggle="tooltip" data-bs-title="{{ __('Nodes') }}"
                class="btn rounded-pill bg-body-secondary-hover">
            <i class="bi-hdd-rack-fill" style="font-size: 1.1rem;"></i>
        </x-link>
    </div>
</div>
{{--<div class="row g-0 gap-3 mb-0 mb-lg-3">--}}
{{--    <div class="col">--}}
{{--        <x-link href="https://github.com/zenonhub-io" target="_blank" class="--}}
{{--            btn w-full--}}
{{--            rounded-pill--}}
{{--            bg-body-secondary-hover border-0 border-lg-1 border-gray-700--}}
{{--        ">--}}
{{--            <i class="bi-github" style="font-size: 1.35rem;"></i>--}}
{{--        </x-link>--}}
{{--    </div>--}}
{{--    <div class="col">--}}
{{--        <x-link href="https://twitter.com/zenonhub" target="_blank" class="--}}
{{--            btn w-full--}}
{{--            rounded-pill--}}
{{--            bg-body-secondary-hover border-0 border-lg-1 border-gray-700--}}
{{--        ">--}}
{{--            <i class="bi-twitter-x" style="font-size: 1.35rem;"></i>--}}
{{--        </x-link>--}}
{{--    </div>--}}
{{--    <div class="col">--}}
{{--        <x-link href="https://t.me/digitalSloth" target="_blank" class="--}}
{{--            btn w-full--}}
{{--            rounded-pill--}}
{{--            bg-body-secondary-hover border-0 border-lg-1 border-gray-700--}}
{{--        ">--}}
{{--            <i class="bi-telegram" style="font-size: 1.35rem;"></i>--}}
{{--        </x-link>--}}
{{--    </div>--}}
{{--</div>--}}




