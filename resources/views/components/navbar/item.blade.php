<li class="nav-item my-1">
    <x-link :href="route($route)" class="nav-link d-flex align-items-center rounded-pill {{ $isActive ? 'active' : null }}">
        @isset($icon)
            <i class="bi bi-{{ $icon }}"></i>
        @endisset

        @isset($svg)
            <x-svg file="{{ $svg }}" class="link ms-n2 me-2" style="height: 16px "/>
        @endisset

        <span>{{ $title }}</span>
    </x-link>
</li>
