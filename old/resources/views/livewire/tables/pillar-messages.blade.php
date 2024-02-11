<div wire:init="shouldLoadResults">
    <div class="m-4">
        @if ($data && $data->count())
            @foreach ($data as $boradcast)
                <div class="{{ ($data->last()->id !== $boradcast->id ? 'border-2 border-bottom border-light pb-4 mb-4' : '') }}">
                    <h4 class="pb-0 {{ ($data->first()->id === $boradcast->id ? 'mt-0 pt-0' : '') }}">
                        {{ $boradcast->title }}
                    </h4>
                    <p>{{ $boradcast->post }}</p>
                    {{ $boradcast->formatted_message }}
                </div>
            @endforeach
            <div class="mt-4">
                {{ $data->links() }}
            </div>
        @else
            <x-alert
                message="Pillar has not broadcast any messages"
                type="info"
                icon="info-circle-fill"
                class="m-0"
            />
        @endif
    </div>
</div>
