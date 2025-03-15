<div class="row g-0 mx-3 mt-3 mb-1 text-center">
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

    @if (! is_hqz())
        <div class="col">
            <x-link :href="route('services.public-nodes')" data-bs-toggle="tooltip" data-bs-title="{{ __('Nodes') }}"
                    class="btn rounded-pill bg-body-secondary-hover">
                <i class="bi-hdd-rack-fill" style="font-size: 1.1rem;"></i>
            </x-link>
        </div>
    @endif
</div>
