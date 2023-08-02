<div wire:init="shouldLoadResults">
    <div class="w-100" wire:loading.delay>
        <div class="m-4 mt-0">
            <div class="row">
                <div class="col-24 col-md-8 offset-md-8">
                    <x-alert
                        message="Processing request..."
                        type="info"
                        icon="arrow-repeat spin"
                        class="d-flex justify-content-center mb-0"
                    />
                </div>
            </div>
        </div>
    </div>
    <div wire:loading.remove>
        @if ($data && $data->count())
            <div class="list-group list-group-flush">
                @foreach($data as $block)
                    <a href="{{ route('explorer.transaction', ['hash' => $block->hash]) }}" class="list-group-item p-4">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ ($block->display_type ?: '-') }}</h5>
                            <small>{{ $block->created_at->format(config('zenon.short_date_format')) }}</small>
                        </div>
                        <pre class="line-numbers mt-2"><code class="lang-json">{{ $block->data->json }}</code></pre>
                    </a>
                @endforeach
            </div>
            <div class="m-4 mt-2">
                {{ $data->links() }}
            </div>
        @elseif($data)
            <div class="m-4 mt-0">
                <x-alert
                    message="No activity found"
                    type="info"
                    icon="info-circle-fill"
                    class="mb-0"
                />
            </div>
        @endif
    </div>
</div>
