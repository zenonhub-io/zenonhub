@props(['socialProfile' => null])

@if ($socialProfile?->website)
    <x-link :navigate="false" href="{{ $socialProfile->website }}" class="link-opacity-50 link-opacity-100-hover">
        <i class="bi bi-globe fs-4"></i>
    </x-link>
@endif
@if ($socialProfile?->email)
    <x-link :navigate="false" href="mailto:{{ $socialProfile->email }}" class="link-opacity-50 link-opacity-100-hover">
        <i class="bi bi-envelope-fill fs-4"></i>
    </x-link>
@endif
@if ($socialProfile?->x)
    <x-link :navigate="false" href="{{ $socialProfile->x }}" class="link-opacity-50 link-opacity-100-hover">
        <i class="bi bi-twitter-x fs-4"></i>
    </x-link>
@endif
@if ($socialProfile?->telegram)
    <x-link :navigate="false" href="{{ $socialProfile->telegram }}" class="link-opacity-50 link-opacity-100-hover">
        <i class="bi bi-telegram fs-4"></i>
    </x-link>
@endif
@if ($socialProfile?->github)
    <x-link :navigate="false" href="{{ $socialProfile->github }}" class="link-opacity-50 link-opacity-100-hover">
        <i class="bi bi-github fs-4"></i>
    </x-link>
@endif
@if ($socialProfile?->medium)
    <x-link :navigate="false" href="{{ $socialProfile->medium }}" class="link-opacity-50 link-opacity-100-hover">
        <i class="bi bi-medium fs-4"></i>
    </x-link>
@endif
