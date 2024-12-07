@props(['text', 'tooltip' => __('Copy')])

<i {{ $attributes->merge(['class' => 'bi bi-copy js-copy pointer']) }}
   data-clipboard-text="{{ $text }}"
   data-bs-toggle="tooltip"
   data-bs-title="{{ $tooltip }}"
></i>
