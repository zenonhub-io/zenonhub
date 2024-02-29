@php($uuid = Str::random(8))

<li class="nav-item my-1">
    <a class="nav-link d-flex align-items-center rounded-pill {{ $isActive ? 'active' : null }}"
       href="#sidebar-{{ $uuid }}"
       data-bs-toggle="collapse"
       role="button" aria-expanded="{{ $isActive ? 'true' : 'false' }}" aria-controls="sidebar-{{ $uuid }}"
    >
        @isset($icon)
            <i class="bi bi-{{ $icon }}"></i>
        @endisset

        @isset($svg)
            <x-svg file="{{ $svg }}" class="link ms-n2 me-2" style="height: 16px"/>
        @endisset

        <span>{{ $title }}</span>
    </a>
    <div class="collapse {{ $isActive ? 'show' : null }}" id="sidebar-{{ $uuid }}">
        <ul class="nav nav-sm flex-column mt-1">
            {{ $slot }}
        </ul>
    </div>
</li>
