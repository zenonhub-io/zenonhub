<x-navbar.nav>
    <x-navbar.item title="Home" icon="house-fill" />
    <x-navbar.item title="Pillars" svg="zenon/pillar" />
    <x-navbar.item title="Sentinels" svg="zenon/sentinel" />
    <x-navbar.item title="Accelerator Z" icon="rocket-takeoff-fill" />

    <x-navbar.dropdown title="Explorer" icon="search" isActive="{{ request()->routeIs('explorer.*') }}">
        <x-navbar.dropdown-item title="Overview" route="explorer" />
        <x-navbar.dropdown-item title="Momentums" route="explorer.momentums" />
        <x-navbar.dropdown-item title="Transactions" route="explorer.transactions" />
        <x-navbar.dropdown-item title="Accounts" route="explorer.accounts" />
        <x-navbar.dropdown-item title="Tokens" route="explorer.tokens" />
        <x-navbar.dropdown-item title="Bridge" route="explorer.bridge" />
        <x-navbar.dropdown-item title="Stakes" route="explorer.stakes" />
        <x-navbar.dropdown-item title="Plasma" route="explorer.plasma" />
    </x-navbar.dropdown>

    <x-navbar.dropdown title="Stats" icon="bar-chart-fill" isActive="{{ request()->routeIs('stats.*') }}">
        <x-navbar.dropdown-item title="Bridge" route="stats.bridge" />
        <x-navbar.dropdown-item title="Public Nodes" route="stats.public-nodes" />
        <x-navbar.dropdown-item title="Accelerator Z" route="stats.accelerator-z" />
    </x-navbar.dropdown>

    <x-navbar.dropdown title="Tools" icon="tools" isActive="{{ request()->routeIs('tools.*') }}">
        <x-navbar.dropdown-item title="Plasma Bot" route="tools.plasma-bot" />
        <x-navbar.dropdown-item title="API Playground" route="tools.api-playground" />
        <x-navbar.dropdown-item title="Broadcast Message" route="tools.broadcast-message" />
        <x-navbar.dropdown-item title="Verify Signature" route="tools.verify-signature" />
    </x-navbar.dropdown>

    <x-navbar.item title="Profile" icon="person-fill" />
</x-navbar.nav>
