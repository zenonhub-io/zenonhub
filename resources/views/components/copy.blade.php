@props(['text', 'tooltip' => __('Copy')])

<i class="bi bi-clipboard ms-1 js-copy" data-clipboard-text="{{ $text }}" data-bs-toggle="tooltip" data-bs-title="{{ $tooltip }}"></i>
