@component('mail::message')
# {{ $pillar->name }} updated their rewards

Current rewards:\
**Momentum {{ $pillar->momentum_rewards }}% | Delegation {{ $pillar->delegate_rewards }}%**

@if ($pillar->previous_history)
Previously:\
**Momentum {{ $pillar->previous_history->momentum_rewards }}% | Delegation {{ $pillar->previous_history->delegate_rewards }}%**
@endif

@component('mail::button', ['url' => route('pillars.detail', [
	'slug' => $pillar->slug,
	'utm_source' => 'notifications',
    'utm_medium' => 'email'
])])
View pillar
@endcomponent
@endcomponent
