<x-cards.card class="mx-4">
    <x-cards.body class="pt-3">
        <div class="vstack text-center">
            <i class="bi bi-badge-ad-fill text-white text-2xl"></i>
            <p class="lead mb-4">
                {{ __('Sponsor us to advertise here') }}
            </p>
            <x-link :href="route('sponsor')" class="btn btn-sm btn-outline-primary w-full">
                {{ __('Lets go!') }} <i class="bi bi-arrow-right ms-2"></i>
            </x-link>
        </div>
    </x-cards.body>
</x-cards.card>
