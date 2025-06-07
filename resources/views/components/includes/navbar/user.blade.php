@if (auth()->check())
    <div class="dropdown">
        <a class="btn d-flex align-items-center py-1 px-3 rounded-pill
            bg-body-secondary-hover border-0 border-lg-1 border-gray-700"
           href="#" role="button"
           data-bs-toggle="dropdown"
           aria-haspopup="false" aria-expanded="false"
        >
            <i class="bi bi-person-circle fs-3"></i> <span class="fs-6 ms-2">
                {{ auth()->user()?->username }}
            </span>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
            <div class="dropdown-header">
                <span class="d-block text-sm text-muted mb-1">{{ __('Signed in as') }}</span>
                <span class="d-block text-heading fw-semibold">{{ auth()->user()?->username ?: 'Guest' }}</span>
            </div>
            <div class="dropdown-divider"></div>
            <x-link :href="route('profile', ['tab' => 'details'])" class="dropdown-item">
                <i class="bi bi-person-fill me-3"></i>{{ __('Details') }}
            </x-link>
            <x-link :href="route('profile', ['tab' => 'security'])" class="dropdown-item">
                <i class="bi bi-shield-fill me-3"></i>{{ __('Security') }}
            </x-link>
            <x-link :href="route('profile', ['tab' => 'notifications'])" class="dropdown-item">
                <i class="bi bi-bell-fill me-3"></i>{{ __('Notifications') }}
            </x-link>
            <x-link :href="route('profile', ['tab' => 'favorites'])" class="dropdown-item">
                <i class="bi bi-star-fill me-3"></i>{{ __('Favorites') }}
            </x-link>
            <x-link :href="route('profile', ['tab' => 'addresses'])" class="dropdown-item">
                <i class="bi bi-wallet2 me-3"></i>{{ __('Addresses') }}
            </x-link>
            <x-link :href="route('profile', ['tab' => 'api-keys'])" class="dropdown-item">
                <i class="bi bi-key-fill me-3"></i>{{ __('API Keys') }}
            </x-link>
            <div class="dropdown-divider"></div>
            <x-buttons.logout :action="route('logout', ['redirect' => url()->current()])" class="dropdown-item">
                <i class="bi bi-lock-fill me-1"></i> {{ __('Logout') }}
            </x-buttons.logout>
        </div>
    </div>
@else
    <x-link :href="route('login')" class="h4 mb-0 fw-bolder" class="btn d-flex align-items-center py-1 px-3 rounded-pill
            bg-body-secondary-hover border-0 border-lg-1 border-gray-700"
    >
        <i class="bi bi-person-circle fs-3"></i> <span class="fs-6 ms-2">
            {{ __('Sign in') }}
        </span>
    </x-link>
@endif
