<div id="main-navbar" class="offcanvas offcanvas-end">
    <div class="offcanvas-header bg-none border-0">
        <h5 class="offcanvas-title">
            Menu
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="navbar-nav d-flex justify-content-end">
            <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }} d-block d-lg-none">
                <a href="{{ route('home') }}" class="nav-link" tabindex="1">
                    <i class="bi bi-house-door-fill fs-lg me-2 opacity-70"></i>
                    Home
                </a>
            </li>
            <li class="nav-item dropdown {{ request()->routeIs('explorer.overview', 'explorer.momentums', 'explorer.transactions', 'explorer.accounts', 'explorer.tokens', 'explorer.momentum', 'explorer.transaction', 'explorer.account', 'explorer.token', 'explorer.staking', 'explorer.fusions') ? 'active' : '' }}">
                <a href="{{ route('explorer.overview') }}" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" tabindex="2">
                    <i class="bi bi-search fs-lg me-2 opacity-70"></i>
                    Explorer
                </a>
                <ul class="dropdown-menu {{ request()->routeIs('explorer.overview', 'explorer.momentums', 'explorer.transactions', 'explorer.accounts', 'explorer.tokens', 'explorer.momentum', 'explorer.transaction', 'explorer.account', 'explorer.token', 'explorer.staking', 'explorer.fusions') ? 'active show' : '' }}">
                    <li><a href="{{ route('explorer.overview') }}" class="dropdown-item {{ request()->routeIs('explorer.overview') ? 'active' : '' }}">Overview</a></li>
                    <li><a href="{{ route('explorer.momentums') }}" class="dropdown-item {{ request()->routeIs('explorer.momentums', 'explorer.momentum') ? 'active' : '' }}">Momentums</a></li>
                    <li><a href="{{ route('explorer.transactions') }}" class="dropdown-item {{ request()->routeIs('explorer.transactions', 'explorer.transaction') ? 'active' : '' }}">Transactions</a></li>
                    <li><a href="{{ route('explorer.accounts') }}" class="dropdown-item {{ request()->routeIs('explorer.accounts', 'explorer.account') ? 'active' : '' }}">Accounts</a></li>
                    <li><a href="{{ route('explorer.tokens') }}" class="dropdown-item {{ request()->routeIs('explorer.tokens', 'explorer.token') ? 'active' : '' }}">Tokens</a></li>
                    <li><a href="{{ route('explorer.staking') }}" class="dropdown-item {{ request()->routeIs('explorer.staking') ? 'active' : '' }}">Staking</a></li>
                    <li><a href="{{ route('explorer.fusions') }}" class="dropdown-item {{ request()->routeIs('explorer.fusions') ? 'active' : '' }}">Fusions</a></li>
                </ul>
            </li>
            <li class="nav-item {{ request()->routeIs('pillars.overview', 'pillars.detail') ? 'active' : '' }}">
                <a href="{{ route('pillars.overview') }}" class="nav-link" tabindex="3">
                    {!! svg('pillar', 'me-2 opacity-70', 'width: 7px') !!}
                    Pillars
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('az.overview', 'az.project', 'az.phase') ? 'active' : '' }}">
                <a href="{{ route('az.overview') }}" class="nav-link" tabindex="4">
                    {!! svg('az', 'me-2 opacity-70', 'width: 14px') !!}
                    Accelerator Z
                </a>
            </li>
            <li class="nav-item dropdown {{ request()->routeIs('tools.overview', 'tools.api-playground', 'tools.verify-signature', 'tools.broadcast-message', 'tools.node-statistics') ? 'active' : '' }}">
                <a href="{{ route('tools.overview') }}" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" tabindex="5">
                    <i class="bi bi-tools fs-lg me-2 opacity-70"></i>
                    Tools
                </a>
                <ul class="dropdown-menu {{ request()->routeIs('tools.overview', 'tools.api-playground', 'tools.verify-signature', 'tools.broadcast-message', 'tools.node-statistics') ? 'show' : '' }}">
                    <li><a href="{{ route('tools.api-playground') }}" class="dropdown-item {{ request()->routeIs('tools.api') ? 'active' : '' }}">API playground</a></li>
                    <li><a href="{{ route('tools.node-statistics') }}" class="dropdown-item {{ request()->routeIs('tools.node-statistics') ? 'active' : '' }}">Node statistics</a></li>
                    <li><a href="{{ route('tools.broadcast-message') }}" class="dropdown-item {{ request()->routeIs('tools.broadcast-message') ? 'active' : '' }}">Broadcast message</a></li>
                    <li><a href="{{ route('tools.verify-signature') }}" class="dropdown-item {{ request()->routeIs('tools.verify-signature') ? 'active' : '' }}">Verify signature</a></li>
                </ul>
            </li>
            @if (auth()->check())
                <li class="nav-item dropdown {{ request()->routeIs('account.overview', 'account.details', 'account.addresses', 'account.security', 'account.notifications') ? 'active' : '' }}">
                    <a href="{{ route('account.overview') }}" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" tabindex="5">
                        <i class="bi bi-person-circle fs-lg me-2 opacity-70"></i>
                        Account
                    </a>
                    <ul class="dropdown-menu {{ request()->routeIs('account.overview', 'account.details', 'account.addresses', 'account.security', 'account.notifications') ? 'show' : '' }}">
                        <li><a href="{{ route('account.details') }}" class="dropdown-item {{ request()->routeIs('account.details') ? 'active' : '' }}">Details</a></li>
                        <li><a href="{{ route('account.notifications') }}" class="dropdown-item {{ request()->routeIs('account.notifications') ? 'active' : '' }}">Notifications</a></li>
                        <li><a href="{{ route('account.addresses') }}" class="dropdown-item {{ request()->routeIs('account.addresses') ? 'active' : '' }}">Addresses</a></li>
                        <li><a href="{{ route('account.security') }}" class="dropdown-item {{ request()->routeIs('account.security') ? 'active' : '' }}">Security</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a href="{{ route('logout') }}" class="dropdown-item">Logout</a></li>
                    </ul>
                </li>
            @else
                <li class="nav-item dropdown {{ request()->routeIs('login') ? 'active' : '' }}">
                    <a href="{{ route('login') }}" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="bi bi-person-circle fs-lg me-2 opacity-70"></i>
                        Account
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('login') }}" class="dropdown-item {{ request()->routeIs('login') ? 'active' : '' }}">Login</a></li>
                        <li><a href="{{ route('sign-up') }}" class="dropdown-item {{ request()->routeIs('sign-up') ? 'active' : '' }}">Signup</a></li>
                    </ul>
                </li>
            @endif
        </ul>
    </div>
    <div class="offcanvas-header border-top">
        @if (auth()->check())
            <a href="{{ route('account.details') }}" class="btn btn-outline-primary w-100">
                <i class="bi-person-circle fs-lg me-2 opacity-70"></i>
                Account
            </a>
        @else
            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
                <i class="bi-unlock fs-lg me-2 opacity-70"></i>
                Login / Signup
            </a>
        @endif
    </div>
</div>
<button type="button" class="navbar-toggler" data-bs-toggle="offcanvas" data-bs-target="#main-navbar" aria-controls="main-navbar" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>
