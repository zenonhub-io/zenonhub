<div class="card border-0 d-none d-lg-block">
    <div class="card-body">
        @foreach ($items as $section => $menuItems)
            <h3 class="h5">{{ $section }}</h3>
            @if (! empty($menuItems))
                <ul class="nav flex-column fs-sm">
                    @foreach($menuItems as $item)
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}" href="{{ route($item['route']) }}">
                                @if (isset($item['icon']))
                                    <i class="bi-{{$item['icon']}} fs-lg opacity-70 me-3"></i>
                                @endif
                                {{ $item['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endforeach
    </div>
</div>
