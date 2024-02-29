<x-cards.card class="my-4">
    <div class="vstack gap-3 text-center">
        <i class="bi bi-rocket-takeoff-fill text-white text-2xl"></i>
        <p class="lead">
            {{ __('Want to promote your brand? Sponsor us to advertise here') }}
        </p>
        <x-link :href="route('sponsor')" class="btn btn-sm btn-outline-primary w-full">
            {{ __('Lets go!') }} <i class="bi bi-arrow-right ms-2"></i>
        </x-link>
    </div>
</x-cards.card>
