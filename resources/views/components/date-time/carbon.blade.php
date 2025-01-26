@if ($human)
    <div {{ $attributes }}>
        <span title="{{ $date->format($format) }}"
            @if($showTooltip) data-bs-toggle="tooltip" data-bs-title="{{ $date->format($format) }}"@endif
        >
        {{ $date->diffForHumans(['parts' => $parts, 'short' => $short], $syntax) }}
        </span>
    </div>
@else
    <div {{ $attributes }}>
        <span title="{{ $date->diffForHumans(['parts' => $parts, 'short' => $short], $syntax) }}"
            @if($showTooltip)data-bs-toggle="tooltip" data-bs-title="{{ $date->diffForHumans(['parts' => $parts, 'short' => $short], $syntax) }}"@endif
        >
            {{ $date->format($format) }}
        </span>
    </div>
@endif
