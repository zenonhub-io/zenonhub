@props(['items' => [], 'active' => null])

<div class="d-none d-md-block">
    <ul class="nav nav-tabs nav-tabs-flush gap-6 overflow-x border-0">
        @foreach($items as $name => $link)
            <li class="nav-item">
                <x-link :href="$link" class="nav-link {{ $active === Str::slug($name) ? 'active' : null }}">
                    {{ $name }}
                </x-link>
            </li>
        @endforeach
    </ul>
</div>
<div class="d-block d-md-none">
    <div class="dropdown">
        <button class="btn btn-sm btn-neutral w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{ Str::headline($active) }}
        </button>
        <ul class="dropdown-menu">
            @foreach($items as $name => $link)
                <li>
                    <x-link :href="$link" class="dropdown-item">
                        {{ $name }}
                    </x-link>
                </li>
            @endforeach
        </ul>
    </div>
</div>
