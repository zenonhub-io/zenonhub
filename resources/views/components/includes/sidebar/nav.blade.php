<x-navbar.nav>
    <x-navbar.item title="Home" icon="house-fill" />
    <x-navbar.item title="Pillars" route="pillar.list" :is-active="request()->routeIs('pillar.*')" svg="zenon/pillar" />
{{--    <x-navbar.item title="Sentinels" route="sentinel.list"  svg="zenon/sentinel" />--}}
    <x-navbar.item title="Accelerator Z" route="accelerator-z.list" :is-active="request()->routeIs('accelerator-z.*')" icon="rocket-takeoff-fill" />

    <x-navbar.dropdown title="Explorer" icon="search" isActive="{{ request()->routeIs('explorer.*') }}">
        <x-navbar.dropdown-item title="Overview" route="explorer.overview" />
        <x-navbar.dropdown-item title="Momentums" route="explorer.momentum.list" isActive="{{ request()->routeIs('explorer.momentum.*') }}" />
        <x-navbar.dropdown-item title="Transactions" route="explorer.transaction.list" isActive="{{ request()->routeIs('explorer.transaction.*') }}" />
        <x-navbar.dropdown-item title="Accounts" route="explorer.account.list" isActive="{{ request()->routeIs('explorer.account.*') }}" />
        <x-navbar.dropdown-item title="Tokens" route="explorer.token.list" isActive="{{ request()->routeIs('explorer.token.*') }}" />
        <x-navbar.dropdown-item title="Bridge" route="explorer.bridge.list" />
        <x-navbar.dropdown-item title="Stakes" route="explorer.stake.list" />
        <x-navbar.dropdown-item title="Plasma" route="explorer.plasma.list" />
    </x-navbar.dropdown>

    <x-navbar.dropdown title="Stats" icon="bar-chart-fill" isActive="{{ request()->routeIs('stats.*') }}">
        <x-navbar.dropdown-item title="Bridge" route="stats.bridge" />
        <x-navbar.dropdown-item title="Public Nodes" route="stats.public-nodes" />
        <x-navbar.dropdown-item title="Accelerator Z" route="stats.accelerator-z" />
    </x-navbar.dropdown>

    <x-navbar.dropdown title="Tools" icon="tools" isActive="{{ request()->routeIs('tools.*') }}">
        <x-navbar.dropdown-item title="Plasma Bot" route="tools.plasma-bot" />
        <x-navbar.dropdown-item title="API Playground" route="tools.api-playground" />
        <x-navbar.dropdown-item title="Verify Signature" route="tools.verify-signature" />
    </x-navbar.dropdown>

    <x-navbar.item title="Profile" icon="person-fill" />
</x-navbar.nav>
