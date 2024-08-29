<x-app-layout>
    <x-includes.header>
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center mb-4">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <x-svg file="zenon/pillar" class="me-4" style="height: 28px"/>
                <x-includes.header-title :title="$pillar->name" />
            </div>
            <div class="d-flex ms-md-auto gap-3">
                <x-link :navigate="false" href="#" class="link-light">
                    <i class="bi bi-envelope-fill fs-4"></i>
                </x-link>
                <x-link :navigate="false" href="#" class="link-light">
                    <i class="bi bi-telegram fs-4"></i>
                </x-link>
                <x-link :navigate="false" href="#" class="link-light">
                    <i class="bi bi-twitter-x fs-4"></i>
                </x-link>
                <x-link :navigate="false" href="#" class="link-light">
                    <i class="bi bi-github fs-4"></i>
                </x-link>
                <x-link :navigate="false" href="#" class="link-light">
                    <i class="bi bi-globe fs-4"></i>
                </x-link>
            </div>
        </div>
    </x-includes.header>

    <div class="row px-3 px-lg-6 mb-6 g-6">
        <div class="col-12 col-md-6">
            <x-cards.card>
                <x-stats.mini-stat
                    :title="__('Weight')"
                    :stat="$pillar->display_weight"
                    info="Total delegated weight"
                />
            </x-cards.card>
        </div>
        <div class="col-12 col-md-6">
            <x-cards.card>
                <x-stats.mini-stat
                    :title="__('Voting')"
                    info="Total delegated weight">
                    @if (! is_null($pillar->az_engagement))
                        <span class="legend-indicator bg-{{ $pillar->az_status_indicator }}"></span>
                        {{ number_format($pillar->az_engagement) }}%
                    @else
                        -
                    @endif
                </x-stats.mini-stat>
            </x-cards.card>
        </div>
        <div class="col-12 col-md-6">
            <x-cards.card>
                <x-stats.mini-stat
                    :title="__('Orchestrator')"
                    info="Total delegated weight">
                    @if($pillar->orchestrator)
                        <span class="legend-indicator bg-{{ ($pillar->orchestrator->is_active ? 'success' : 'danger') }}"></span>
                        {{ ($pillar->orchestrator->is_active ? 'Online' : 'Offline') }}
                    @else
                        -
                    @endif
                </x-stats.mini-stat>
            </x-cards.card>
        </div>
        <div class="col-12 col-md-6">
            <x-cards.card>
                <x-stats.mini-stat
                    :title="__('Rewards')"
                    info="Total delegated weight">
                    {{ $pillar->momentum_rewards }} / {{ $pillar->delegate_rewards }}
                </x-stats.mini-stat>
            </x-cards.card>
        </div>
    </div>

    <x-cards.card class="mx-3 mx-lg-6 mb-6">
        <div class="row">
            <div class="col-24 col-md-12">
                <div class="vstack gap-3">
                    <x-stats.list-item :title="__('Spawned')">
                        <x-date-time.carbon :date="$pillar->created_at" />
                    </x-stats.list-item>
                    <x-stats.list-item :title="__('Registration cost')" :stat="$pillar->display_qsr_burn" />
                    <x-stats.list-item :title="__('Revokable in')" :stat="$pillar->display_revocable_in" :hr="false"/>
                </div>
            </div>
            <div class="col-24 col-md-12">
                <div class="vstack gap-3">
                    <x-stats.list-item :title="__('Owner')">
                        <x-address :account="$pillar->owner" :named="false"/>
                    </x-stats.list-item>
                    <x-stats.list-item :title="__('Producer')">
                        <x-address :account="$pillar->producerAccount" :named="false"/>
                    </x-stats.list-item>
                    <x-stats.list-item :title="__('Withdraw')" :hr="false">
                        <x-address :account="$pillar->withdrawAccount" :named="false"/>
                    </x-stats.list-item>
                </div>
            </div>
        </div>
    </x-cards.card>



    <x-includes.header>
        <x-navigation.header.responsive-nav :items="[
            __('Delegators') => route('pillar.detail', ['slug' => $pillar->slug, 'tab' => 'delegators']),
            __('Votes') => route('pillar.detail', ['slug' => $pillar->slug, 'tab' => 'votes']),
            __('Updates') => route('pillar.detail', ['slug' => $pillar->slug, 'tab' => 'updates']),
            __('JSON') => route('pillar.detail', ['slug' => $pillar->slug, 'tab' => 'json'])
        ]" :active="$tab" />
    </x-includes.header>

    @if ($tab === 'delegators')

    @endif

    @if ($tab === 'votes')

    @endif

    @if ($tab === 'updates')

    @endif

    @if ($tab === 'json')
        <pre class="line-numbers">
            <code class="lang-json">
                {{ json_encode($pillar->raw_json, JSON_PRETTY_PRINT) }}
            </code>
        </pre>
    @endif

</x-app-layout>

