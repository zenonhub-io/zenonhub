<div>
    <div class="d-none d-md-block">
        <ul class="nav nav-tabs nav-tabs-flush gap-6 overflow-x border-0">
            @foreach($items as $name => $tab)
                <li class="nav-item">
                    <a href="#{{ $tab }}" class="nav-link {{ $activeTab === $tab ? 'active' : null }}" wire:click="$dispatch('tab-changed', { tab: '{{ $tab }}'})">
                        {{ $name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="d-block d-md-none">
        <div class="dropdown">
            <button class="btn btn-sm btn-neutral w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ Str::headline($activeTab) }}
            </button>
            <ul class="dropdown-menu">
                @foreach($items as $name => $tab)
                    <li>
                        <a href="#{{ $tab }}" class="dropdown-item" wire:click="$dispatch('tab-changed', { tab: '{{ $tab }}'})">
                            {{ $name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
