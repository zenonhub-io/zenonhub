<div>
    <x-cards.card>
        <x-cards.heading class="d-flex align-items-center">
            <div class="title-icon me-4">
                <x-svg file="zenon/pillar" />
            </div>
            <h4 class="flex-grow-1 mb-0">
                {{ __('Pillars') }}
            </h4>
            <x-link :href="route('pillar.list')" class="btn btn-xs btn-outline-primary">
                {{ __('All') }}
                <i class="bi bi-arrow-right ms-2"></i>
            </x-link>
        </x-cards.heading>
        <ul class="list-group list-group-flush mb-0">
            @foreach ($pillars as $pillar)
                <li class="list-group-item d-flex align-items-start justify-content-between px-4">
                    <div class="d-block">
                        <x-link :href="route('pillar.detail', ['slug' => $pillar->slug])">
                            <div class="d-flex align-items-center mb-1">
                                <div class="title-icon me-2">
                                    @if ($pillar->socialProfile?->avatar)
                                        <img src="{{ $pillar->socialProfile?->avatar }}" class="rounded" alt="{{ $pillar->name }} Logo"/>
                                    @else
                                        <x-svg file="zenon/pillar" />
                                    @endif
                                </div>
                                {{ $pillar->name }}
                            </div>
                        </x-link>
                        <span class="text-xs d-block text-muted">{{ __('Weight') }} {{ $pillar->display_weight }}</span>
                    </div>
                    <div class="d-block text-end">
                        <span class="badge text-bg-light bg-opacity-75">#{{ $pillar->display_rank }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    </x-cards.card>
</div>
