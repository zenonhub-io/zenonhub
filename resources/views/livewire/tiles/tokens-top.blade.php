<div>
    <x-cards.card>
        <x-cards.heading class="d-flex align-items-center">
            <div class="title-icon me-3">
                <x-svg file="zenon/az" />
            </div>
            <h4 class="flex-grow-1 mb-0">
                {{ __('Tokens') }}
            </h4>
            <x-link :href="route('explorer.token.list')" class="btn btn-xs btn-outline-primary">
                {{ __('All') }}
                <i class="bi bi-arrow-right ms-2"></i>
            </x-link>
        </x-cards.heading>
        <ul class="list-group list-group-flush mb-0">
            @foreach ($tokens as $token)
                <li class="list-group-item d-flex align-items-start justify-content-between px-4">
                    <div class="d-block">
                        <x-link :href="route('explorer.token.detail', ['zts' => $token->token_standard])">
                            {{ $token->name }}
                        </x-link>
                        <span class="text-xs d-block text-muted">{{ $token->symbol }}</span>
                    </div>
                    <div class="d-block text-end">
                        <span class="badge text-bg-light bg-opacity-75">{{ $token->holders_count }} {{ __('Holders') }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    </x-cards.card>
</div>
