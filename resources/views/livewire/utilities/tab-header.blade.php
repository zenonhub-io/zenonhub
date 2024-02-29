<div>
    <div class="d-md-none">
        <select id="tab-controls" class="form-select" wire:change="$dispatch('tabChanged', {tab: $event.target.value})">
            @foreach($tabs as $tab => $title)
                <option value="{{ $tab }}" {{ $tab === $activeTab ? 'selected' : '' }}>{{ $title }}</option>
            @endforeach
        </select>
    </div>
    <div class="d-none d-md-block">
        <ul class="nav nav-tabs-alt card-header-tabs">
            @foreach($tabs as $tab => $title)
                <li class="nav-item">
                    <button class="btn nav-link {{ $tab === $activeTab ? 'active' : '' }}" wire:click="$dispatch('tabChanged', {tab: '{{ $tab }}'})">
                        {{ $title }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>
