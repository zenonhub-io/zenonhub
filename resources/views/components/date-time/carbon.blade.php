@if ($human)
    <div {{ $attributes }}>
        <span title="{{ $date->format($format) }}" data-bs-toggle="tooltip" data-bs-title="{{ $date->format($format) }}">
        {{ $date->diffForHumans(['parts' => $parts], $syntax) }}
        </span>
    </div>
@else
    <div {{ $attributes }}>
        <span title="{{ $date->diffForHumans(['parts' => $parts], $syntax) }}"
              data-bs-toggle="tooltip" data-bs-title="{{ $date->diffForHumans(['parts' => $parts], $syntax) }}">
            {{ $date->format($format) }}
        </span>
    </div>
@endif
