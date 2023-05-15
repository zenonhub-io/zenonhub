@component('mail::message')
# {{ $pillar->name }} has been registered

Current rewards:
### Momentum {{ $pillar->momentum_rewards }}%
### Delegation {{ $pillar->delegate_rewards }}%

@component('mail::button', ['url' => route('pillars.detail', [
    'slug' => $pillar->slug,
    'utm_source' => 'notifications',
    'utm_medium' => 'email'
])])
View pillar
@endcomponent
@endcomponent
