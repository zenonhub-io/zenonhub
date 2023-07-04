<div>
    @if ($data && $data->count())
        @foreach ($data as $phase)
            <div class="mb-4 card shadow card-hover {{ ($data->last()->id !== $phase->id ? 'mb-4' : '') }}" id="phase-{{ $phase->hash }}">
                <div class="card-header border-bottom">
                    <span class="float-end">
                        {!! $phase->display_badge !!}
                    </span>
                    <div class="text-muted">
                        Phase {{ $phase->phase_number }}
                    </div>
                    <h5 class="mb-0">
                        <a href="{{ route('az.phase', ['hash' => $phase->hash]) }}">
                            {{ $phase->name }}
                        </a>
                    </h5>
                </div>
                <div class="card-body mb-0">
                    <div class="row">
                        <div class="col-24">
                            <a href="{{ route('az.phase', ['hash' => $phase->hash]) }}">
                                <x-az-card-header :item="$phase"/>
                            </a>
                        </div>
                        <div class="col-24">
                            <ul class="list-group list-group-flush mb-0">
                                <li class="list-group-item">
                                    {{ $phase->description }}
                                </li>
                                <li class="list-group-item">
                                    <span class="d-block fs-sm text-muted">Link</span>
                                    <a href="{{ $phase->url }}" target="_blank">{{ $phase->url }}</a>
                                </li>
                                <li class="list-group-item">
                                    <span class="d-block fs-sm text-muted">Created</span>
                                    {{ $phase->created_at->format(config('zenon.date_format')) }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="mt-4">
            {{ $data->links() }}
        </div>
    @endif
</div>
