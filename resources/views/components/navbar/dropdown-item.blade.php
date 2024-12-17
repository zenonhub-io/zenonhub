<li class="nav-item">
    <x-link :href="route($route)" class="nav-link {{ $isActive ? 'fw-bold' : null }}">
        {{ $title }}
    </x-link>
</li>
